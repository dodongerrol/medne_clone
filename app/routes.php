<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
// test spending transaction access
Route::get('test_spending_transaction_access', 'HomeController@testUserCheckSpendingAccess');

// test paginate new
Route::get('ge_test_paginate', 'testcontroller@paginateMembers');
// Route::get('test_return_balance', 'testcontroller@testReturnBalance');
Route::get('test/email_send', 'HomeController@testEmailSend');

// test recalculate balance
Route::get('test_balance', 'testcontroller@testBalance');
// test member credit reset dates
// Route::get('test_get_member_reset_dates', 'testcontroller@getMemberResetDateTest');
// Route::get('test_customer_reset_credits_dates', 'testcontroller@testCustomerResetDates');
// Route::get('test_member_reset_credits_dates', 'testcontroller@testMemberResetDates');
// test wallet balance for reset credits
Route::post('test_spending_balance', 'testcontroller@testGetMedicalBalanceByDate');
// test date format
// Route::post('test_format_date', 'testcontroller@testFormatDate');
// test upload e-claim
// Route::post("test_upload_e_claim_queue",'testcontroller@testEclaimUploadQueue');
// test send e-claim emial
// Route::post('test_send_e_claim', 'testcontroller@testEclaimSendEmail');
// get currencies
Route::get('get/currency_lists', 'testcontroller@getCurrencyLists');
// Route::get('image_cloudinary_auto_quality', 'testcontroller@ImageAutoQuality');
Route::get('member/first_time_login', 'HomeController@firstTimeLogin');
// Route::post('upload_image', 'testcontroller@uploadImage');
// Route::get('update_clinic_default_image', 'testcontroller@updateClinicDefaultImage');

// Route::get('test_employee_reset', 'testcontroller@getUserEmployee');
// test rouote for sms password reset
// Route::post('test_sms', 'testcontroller@testSMSsend');
// test route for sms enroll
// Route::post('test_sms_enroll', 'testcontroller@testSendSmsEnroll');
// test nric
// Route::post('test_nric', 'testcontroller@testNRIC');
// test employee plan coverage status
// Route::get('test_employee_plan_coverage', 'testcontroller@testGetuserPlanCoverage');

// cron jobs
// care plan expiration
Route::get('cron/get_customer_expire_plan', 'BenefitsDashboardController@getCompanyExpirePlan');
// cron for activate user
Route::get('activate/care_plan_user', 'CarePlanPurchaseController@activateCarePlanUser');
// cron for generate invoice
Route::get('generate/clinic/invoice', 'InvoiceController@generateMonthlyInvoice');
// Cron jobs for statement of account
Route::get('app/cron/statement_of_account', 'CronController@generateStatementOfAccount');
// cron job for corporate monthly invoice
// Route::get('app/cron/company_invoice', 'EclaimController@generateMonthlyCompanyInvoice');
Route::get('app/cron/company_invoice', 'SpendingInvoiceController@generateMonthlyCompanyInvoice');
// generate invoice
// Route::get('generate/clinic/invoice', 'InvoiceController@generateMonthlyInvoice');
// crom for withdrawal deletion account
Route::get('app/cron/withdrawal_deletion_account', 'CronController@createAutomaticDeletion');
// cron for activate replace new employee
Route::get('app/cron/replace_new_employee', 'CronController@activateReplaceNewEmployee');
// cron for activate remove employee
Route::get('app/cron/activate_remove_employee', 'CronController@activateRemoveReplaceEmployee');
// cron for deactive employee seat
Route::get('app/cron/remove_employee_seat', 'CronController@removeEmployeeSeat');
// Route::get('app/cron/withdrawal_deletion_account', 'BenefitsDashboardController@createAutomaticDeletion');

// remove dependents from employee deletion
Route::get('app/cron/remove_dependent_from_employee', 'CronController@removeDepdentsEmployeeAccounts');

// test api for deactivate user
Route::get('test_deactivate_user', 'HomeController@testDeactivateUser');
// pdf for members coverage
Route::get('pdf/Members', 'HomeController@getMembersPdf');

// get notification config
Route::get('config/notification', 'HomeController@getNotificationConfig');

// test get file as json
// Route::post('test/get_file', 'BenefitsDashboardController@testGetExcel');


// THIRD PARTY ACCESS
Route::get('validate/member', 'ThirdPartyAccessController@checkMember');

// EMPLOYEE UPDATE EXERCISES
Route::group(array('prefix' => 'exercise'), function()
{
	Route::post('validate_member', 'EmployeeController@validateMember');
	Route::get('get_member_details', 'EmployeeController@getEmployeeDetails');
	Route::post('update_member_details', 'EmployeeController@updateEmployeeDetails');
	Route::post('validate_mobile_number', 'EmployeeController@checkMobileExistence');
	Route::post('send_sms_otp', 'EmployeeController@sendMemberSmsOtp');
	Route::post('validate_otp_code', 'EmployeeController@validateOpt');
});


Route::get('app/e_claim', 'HomeController@oldeClaim');
Route::get('member-portal-login', 'HomeController@eClaimLogin');
Route::get('member-portal', 'HomeController@eClaimHome');
// LOGIN FOR ECLAIM EMPLOYEES
Route::post('app/e_claim/login', 'EclaimController@loginEmployee');
// Route::get('app/e_claim/logout', 'EclaimController@logoutEmployee');


Route::get('app/resetcompanypassword', 'HomeController@getCompanyForgotPasswordView');
Route::get('app/resetmemberpassword', 'HomeController@getMemberForgotPasswordView');
Route::get('app/resetclinicpassword', 'HomeController@getClinicForgotPasswordView');

Route::get('download/transaction_receipt/{transaction_id}', 'BenefitsDashboardController@downloadTransactionReceipt');
Route::post('hr/create-password-activated', 'BenefitsDashboardController@createCompanyPasswordActivated');
Route::post('employee/check_email_validation', 'EmployeeController@checkEmailValidation');
Route::post('hr/employee_reset_password', 'EmployeeController@employeeResetPassword');
// admin resend activation email
Route::post('hr/resend_activation_email', 'CorporateController@resendCorporateActivationEmail');
// authentications for eclaim
Route::group(array('before' => 'auth.jwt_employee'), function( ){
	Route::get('employee/get/user_details', 'EclaimController@getUserData');
	// Route::post('app/create_e_claim', 'EclaimController@createEclaimMedical');
	Route::get('app/get_e_claims', 'EclaimController@getEclaims');
	Route::get('app/get_e_claim_details/{id}', 'EclaimController@getEclaimDetails');
	// upload image
	Route::post('app/image_upload', 'EclaimController@uploadImage');
	// logout
	Route::get('app/e_claim/logout', 'EclaimController@logout');
	// get user coverage
	Route::get('app/e_claim/user_coverage', 'EclaimController@getUserCoverage');
	// update profile
	Route::post('app/e_claim/update_profile', 'EclaimController@updateProfile');
	// get employee current spending for medical
	Route::get('employee/get_current_spending', 'EclaimController@currentSpending');
	// get employee current spending for wellness
	Route::get('employee/get_current_wellness_spending', 'EclaimController@currentSpendingWellness');
	// get employee members
	Route::get('employee/get_members', 'EclaimController@getEmployeeMembers');
	// get employee medical activity
	Route::post('employee/search_eclaim_activity', 'EclaimController@getActivity');
	// create e claim
	Route::post('employee/create/e_claim', 'EclaimController@createEclaimMedical');
	Route::post('employee/save/e_claim_receipt', 'EclaimController@createEclaimReceipt');
	Route::post('employee/create/e_claim_receipt', 'EclaimController@saveEclaim');
	Route::post('employee/create/transaction_receipt', 'EclaimController@createInNetworkReceipt');
	// change password
	Route::post('employee/change_password', 'EclaimController@updateEmployeePassword');
	// create e-claim wellness
	Route::post('employee/create/e_claim_wellness', 'EclaimController@createEclaimWellness');
	// get employee wellness activity
	Route::post('employee/search_eclaim_wellness_activity', 'EclaimController@getWellnessActivity');
	// get health partner lists
	Route::get('employee/get_health_partner_lists', 'EclaimController@getHealthPartnerLists');
	// get user care package
	Route::get('employee_care_package', 'BenefitsDashboardController@employeePackages');
	// get doc presigned url
	Route::get('employee_care_package/get_e_claim_doc', 'EclaimController@getPresignedEclaimDoc');
	// check Employee e-claim submission visit date
	Route::post('employee/check_e_claim_visit', 'EclaimController@checkEClaimDatesBalance');
	// get date terms
	Route::get('employee/get_date_terms', 'EmployeeController@getEmployeeDateTerms');
});
	Route::post('employee/add_postal_code_member', 'EmployeeController@addPostalCodeEmployee');
	Route::post('employee/create_new_password_member', 'EmployeeController@createNewPasswordEmployee');
	Route::get('employee/check_user_otp_status', 'EmployeeController@checkUserOtp');
	Route::post('employee/send_otp_web', 'EmployeeController@sendOtpWeb');
	Route::get('employee/check_member', 'EmployeeController@checkMember');
	Route::post('employee/validate_otp_web', 'EmployeeController@validateOtpWeb');
	Route::post('employee/check_member_password', 'EmployeeController@confirmMemberPassword');

// api for getting local_network
Route::get('list/local_network', 'NetworkPatnerController@getLocalNetworkList');
Route::get('list/local_network_partners/{id}', 'NetworkPatnerController@getLocalNetworkPartnerList');

// Route::post('generate/clinic/invoice', 'InvoiceController@createClinicInvoice');

// hr dashboard
Route::get('business-portal-login', 'HomeController@hrDashboardLogin');
Route::get('company-activation', 'HomeController@getCompanyActivationView');
Route::get('company-benefits-dashboard-login', 'HomeController@oldhrDashboardLogin');
Route::get('company-benefits-dashboard-logout', 'BenefitsDashboardController@logOutHr');
Route::post('company-benefits-dashboard-login', 'BenefitsDashboardController@hrLogin');
Route::get('hr/reset-password-details/{token}', 'BenefitsDashboardController@getHrPasswordTokenDetails');
Route::get('hr/validate_token', 'BenefitsDashboardController@getTokenDetails');
Route::post('hr/reset-password-data', 'BenefitsDashboardController@resetPasswordData');
Route::post('hr/create-company-password', 'BenefitsDashboardController@createCompanyPassword');

// hr member otp
Route::post('hr/member_hr_vaidated_otp', 'HrController@confirmHrAdminOtp');

// create resend hr activation link
Route::post('hr/resend_hr_activation_link', 'BenefitsDashboardController@resendHrActivationLnk');
// secure route on hr page, need authenticated to get access on this routes

Route::get('company-benefits-dashboard', 'HomeController@hrDashboard');
Route::get('company-benefits-dashboard-forgot-password', 'HomeController@hrForgotPassword');
Route::post('hr/forgot/company-benefits-dashboard', 'BenefitsDashboardController@forgotPassword');

// benefits invoice
Route::get('benefits/invoice', 'BenefitsDashboardController@benefitsInvoice');
// benefits receipt
Route::get('benefits/receipt', 'BenefitsDashboardController@benefitsReceipt');
// spending deposit
Route::get("benefits/deposit/{id}", 'BenefitsDashboardController@getSpendingDeposit');
Route::get('hr/get_cancellation_details', 'BenefitsDashboardController@refundDetails');
// Route::get('hr/statement_download', 'BenefitsDashboardController@downloadStatementPDF');

Route::get('hr/statement_download', 'SpendingInvoiceController@downloadSpendingInvoice');
// Route::get('hr/statement_in_network_download', 'BenefitsDashboardController@downloadStatementInNetwork');

Route::get('hr/statement_in_network_download', 'SpendingInvoiceController@downloadSpendingInNetwork');
// Route::get('hr/statement_eclaim_download', 'BenefitsDashboardController@downloadStatementEclaim');
Route::get('hr/statement_eclaim_download', 'SpendingInvoiceController@downloadStatementEclaim');
// download receipt spend
Route::get('hr/download_spending_receipt', 'BenefitsDashboardController@downloadSpendingReceipt');
// download spending deposit
Route::get('hr/spending_desposit', 'BenefitsDashboardController@getSpendingDeposit');
Route::get('hr/calculate_added_headcount', 'BenefitsDashboardController@calculatePrices');
Route::get('hr/download_dependent_invoice', 'DependentController@getDependentInvoice');

// get pending employee replacement
Route::get('hr/get_pending_employee_deactivate', 'BenefitsDashboardController@getPendingEmployeeDeactivate');

// update agree status
Route::get('update/agree_status', 'BenefitsDashboardController@updateAgreeStatus');
Route::group(array('before' => 'auth.jwt_hr'), function( ){


	// get token download

	// gods view
	// Departments
	Route::get('hr/get_department_list', 'BenefitsDashboardController@getDepartmentList');
	Route::post('hr/create_department', 'BenefitsDashboardController@createHrDepartment');
	Route::post('hr/update_department', 'BenefitsDashboardController@updateHrDepartment');
	Route::post('hr/remove_department', 'BenefitsDashboardController@deleteHrDepartment');
	Route::post('hr/allocate_employee_department', 'BenefitsDashboardController@allocateEmployeeDepartment');

	// locations
	Route::post('hr/create_locations', 'BenefitsDashboardController@createHrLocation');
	Route::get('hr/get_location_list', 'BenefitsDashboardController@getLocationList');
	Route::post('hr/update_location', 'BenefitsDashboardController@updateHrLocation');
	Route::get('hr/remove_location', 'BenefitsDashboardController@deleteHrLocation');
	Route::get('hr/get_allocate_employee_list', 'BenefitsDashboardController@allocateEmployeeLocationList');
	Route::post('hr/allocate_employee_location', 'BenefitsDashboardController@allocateEmployeeLocation');

	// business contact
	Route::post('hr/add_more_business_contact', 'BenefitsDashboardController@addMoreBusinessContact');
	Route::get('hr/get_business_contact', 'BenefitsDashboardController@getHrBusinessContact');
	Route::get('hr/get_company_contacts', 'BenefitsDashboardController@getHrCompanyContact');
	Route::post('hr/update_business_contact', 'BenefitsDashboardController@updateHrBusinessContact');
	Route::post('hr/update_company_contact', 'BenefitsDashboardController@updateHrCompanyContact');

	// business information
	Route::get('hr/get_business_information', 'BenefitsDashboardController@getHrBusinessInformation');
	Route::post('hr/update/business_information', 'BenefitsDashboardController@updateHrBusinessInformation');

	// billing contact
	Route::post('hr/update/billing_contact', 'BenefitsDashboardController@updateHrBillingContact');
	Route::get('hr/get_billing_contact', 'BenefitsDashboardController@getHrBillingContact');

	// billing information
	Route::post('hr/update/billing_information', 'BenefitsDashboardController@updateBillingInformation');
	Route::get('hr/get_billing_information', 'BenefitsDashboardController@getBillingInformation');

	// Administrator
	Route::post('hr/add_employee_admin', 'BenefitsDashboardController@addAdministrator');
	Route::get('hr/get_primary_admin_details', 'BenefitsDashboardController@getPrimaryAdminDetails');
	Route::get('hr/get_additional_admin_details', 'BenefitsDashboardController@getAdditionalAdminDetails');
	Route::get('/hr/remove_additional_administrator', 'BenefitsDashboardController@removeAdministratorAccount');
	Route::post('hr/update_administrator', 'BenefitsDashboardController@updateAdministrator');

	Route::get("hr/get_download_token", "BenefitsDashboardController@getDownloadToken");
	Route::post('hr/new_purchase_active_plan/excel', 'BenefitsDashboardController@newPurchaseFromExcel');
	Route::get('hr/get_plan_status', 'BenefitsDashboardController@getPlanStatus');
	Route::get('get-hr-session', 'BenefitsDashboardController@hrStatus');
	Route::get('hr/check_plan', 'BenefitsDashboardController@checkPlan');
	Route::get('hr/update_show_done', 'BenefitsDashboardController@updateShowDone');
	Route::get('hr/enrollment_progress', 'BenefitsDashboardController@employeeEnrollmentProgress');
	Route::get('hr/get/temp_enrollment', 'BenefitsDashboardController@getTempEnrollment');
	Route::get('remove/temp_enrollee/{id}', 'BenefitsDashboardController@removeEnrollee');
	// Delete all existing temp employees in enrollment summary
	Route::get('delete_all_temp_employees', 'BenefitsDashboardController@removeAllEnrolleeTemp');
	Route::post('insert/enrollee_web_input', 'BenefitsDashboardController@insertFromWebInput');
	Route::post('update/enrollee_details', 'BenefitsDashboardController@updateEnrolleeDetails');
	// Route::post('hr/finish/enroll', 'BenefitsDashboardController@finishEnroll');
	
	// upload via excel
	// Route::post('upload/excel_enrollment', 'BenefitsDashboardController@uploadExcel');
	Route::post('upload/excel_enrollment', 'DependentController@uploadExcel');
	// finish employee enrollements
	// Route::post('hr/finish/enroll', 'BenefitsDashboardController@finishEnroll');
	// employee list
	Route::get('hr/employee/list', 'BenefitsDashboardController@employeeLists');
	Route::get('hr/employee/{id}', 'BenefitsDashboardController@employeeByID');
	Route::get('hr/company_allocation', 'BenefitsDashboardController@userCompanyCreditsAllocated');
	// search employee
	Route::post('hr/search/employee', 'BenefitsDashboardController@searchEmployee');
	// update employee details
	Route::post('hr/employee/update', 'BenefitsDashboardController@updateEmployeeDetails');
	// get company details and contacts
	Route::get('hr/company_contacts', 'BenefitsDashboardController@getCompanyContacts');
	// get transactions
    Route::get('hr/transactions', 'BenefitsDashboardController@benefitsTransactions');
    // get hr benefits spending invoice
	// Route::get('hr/benefits_spending_invoice_transactions', 'BenefitsDashboardController@getHrBenfitSpendingInvoice');
	Route::get('hr/benefits_spending_invoice_transactions', 'SpendingInvoiceController@getHrBenfitSpendingInvoice');
	// remove employee
	// Route::get('hr/remove_employee/{id}', 'BenefitsDashboardController@removeEmployee');
	// withdraw empoyes
	Route::post('hr/employees/withdraw', 'BenefitsDashboardController@withDrawEmployees');
	// get refund data
	Route::get('hr/get_refunds', 'BenefitsDashboardController@getWithdrawEmployees');
	// get account and billing data
	Route::get('hr/account_billing', 'BenefitsDashboardController@accountBilling');
	// update business contact
	Route::post('hr/update/business_contact', 'BenefitsDashboardController@updateBusinessContact');
	// update billing address
	Route::post('hr/update/billing_address', 'BenefitsDashboardController@updateBillingAddress');
	// update payment method
	Route::post('hr/update/payment_method', 'BenefitsDashboardController@updatePaymentMethod');
	// get task list
	Route::get('hr/task_list', 'BenefitsDashboardController@taskList');
	// get runded data list
	Route::get('hr/get_runded_lists/{id}', 'BenefitsDashboardController@viewRefundedUserList');
	// get company credits and user credits
	Route::get('hr/employee/credits/{id}', 'BenefitsDashboardController@getEmployeeCredits');
	// assign credits to employee
	Route::post('hr/employee/assign_credits', 'BenefitsDashboardController@employeeAssignCredits');
	// double confirm password
	Route::post('hr/password', 'BenefitsDashboardController@confirmPassword');
	// company credits password
	Route::get('hr/credits', 'BenefitsDashboardController@companyCredits');
	// get active plan data
	Route::get('hr/get_active_plan/{id}', 'BenefitsDashboardController@getActivePlan');
	// allocate user
	Route::post('hr/employee/allocate_credits', 'BenefitsDashboardController@allocateEmployeeCredits');
	// deduct user
	Route::post('hr/employee/deduct_credits', 'BenefitsDashboardController@deductEmployeeCredits');
	// get all employee with credits
	Route::get('hr/all_employee_credits/{per_page}', 'BenefitsDashboardController@getAllUserWithCredits');
	// search user
	Route::post('hr/search_company_employee_credits', 'BenefitsDashboardController@searchCompanyEmployeeCredits');
	// chec company balance
	Route::get('hr/check_balance', 'BenefitsDashboardController@userCompanyCreditsAllocated');
	// save type of payment added new active plan
	Route::post('hr/save/web_input/new_active_plan', 'BenefitsDashboardController@newPurchaseFromWebInput');
	// save type of payment and if cheque is use finish enroll from added new active plan
	// Route::post('hr/save/payment/method/new_active_plan', 'BenefitsDashboardController@paymentMethod');
	Route::post('hr/save/payment/method/new_active_plan', 'BenefitsDashboardController@newPaymentAddedPurchaseEmployee');
	// get hr activity
	Route::get('hr/get_activity', 'EclaimController@getHrActivity');
	Route::get('hr/get_spending_invoice_history_list', 'InvoiceController@spendingInvoiceHistoryList');
	
	// search employee activity
	Route::post('hr/search_employee_activity', 'EclaimController@searchEmployeeActivity');
	// search employee e-claim activity
	Route::post('hr/search_employee_e_claim_activity', 'EclaimController@searchEmployeeEclaimActivity');
	// get company members
	Route::get('hr/employee_lists', 'BenefitsDashboardController@getCompanyMembers');
	// get hr e-claim approval
	Route::get('hr/e_claim_activity', 'EclaimController@hrEclaimActivity');
	// give e_claim status************
	Route::post('hr/e_claim_update_status', 'EclaimController@updateEclaimStatus');
	// get hr statement
	// Route::post('hr/get_statement', 'EclaimController@createHrStatement');
	Route::post('hr/get_statement', 'SpendingInvoiceController@createHrStatement');
	// get hr full_statement
	Route::post('hr/get_full_statement', 'EclaimController@getStatementFull');
	// search employee statement
	Route::post('hr/search_employee_statement', 'EclaimController@searchEmployeeStatement');
	// remove temp enrollees
	Route::post('hr/remove_enrollees', 'BenefitsDashboardController@removeEnrollees');
	Route::get('get/active_plan_hr', 'BenefitsDashboardController@getActivePlanHr');
	// update hr password
	Route::post('hr/update_password', 'BenefitsDashboardController@updateHrPassword');
	// get hr details
	Route::get('hr/get_hr_details', 'BenefitsDashboardController@getHrDetails');
	// update hr account details
	Route::post('hr/update_hr_details', 'BenefitsDashboardController@updateHrAccountDetails');
	// get cancellation details
	Route::get('hr/get_head_count_plan/{id}', 'BenefitsDashboardController@getAddedHeadCountInvoice');
	
	// get hr credits total allocation
	Route::get('hr/total_credits_allocation', 'BenefitsDashboardController@getCompanyTotalAllocation');
	// get company plan status
	Route::get('hr/get_company_plan_status', 'BenefitsDashboardController@getCompanyPlanDetails');
	// get company active plan
	Route::get('hr/company_active_plans', 'BenefitsDashboardController@getPlanActivePlans');
	// get active plan details
	Route::get('hr/active_plan_details/{id}', 'BenefitsDashboardController@getActivePlanDetails');
	// get spending deposits
	Route::get('hr/get_spending_deposits', 'BenefitsDashboardController@getSpendingDeposits');
	// get company employees and credits left
	// Route::get('hr/get_company_employee_lists_credits', 'BenefitsDashboardController@newGetCompanyEmployeeWithCredits');
	Route::get('hr/details', 'BenefitsDashboardController@getCompanyDetails');


	// plan tier and dependents api
	
	Route::post("hr/create/employee_enrollment", "PlanTierController@createWebInputTier");
	Route::get("hr/get/plan_tier_enrolless", "PlanTierController@getPlanTierEnrollment");
	Route::post("hr/update/tier_employee_enrollee_details", "PlanTierController@updatePlanTierEmployeeEnrollment");
	Route::post("hr/update_tier_dependent_enrollee_details", "PlanTierController@updatePlanTierDependentEnrollment");
	Route::post("hr/update_plan_tier", "PlanTierController@updatePlanTier");
	Route::post("hr/create/employee_user", "PlanTierController@finishEnrollEmployeeTier");
	Route::get("hr/get_plan_tiers", "PlanTierController@getPlanTiers");
	// dependents new apis
	Route::get('hr/get_dependent_status', 'DependentController@getDepdentsCount');
	Route::get('hr/get_intro_overview', 'BenefitsDashboardController@getIntroMessage');
	Route::get('hr/get_current_plan_total_due', 'BenefitsDashboardController@getCompanyPlanDueAmount');
	Route::get('hr/get_current_spending_total_due', 'BenefitsDashboardController@getSpendingPendingAmountDue');
	
	Route::get('hr/get_total_members', 'BenefitsDashboardController@totalMembers');
	// replace employee
	Route::post('hr/employee/replace', 'BenefitsDashboardController@replaceEmployee');
	// new upload excel
	// test routes for dependents
	// Route::post("upload_excel_dependents", "DependentController@uploadExcel");
	// get hr activity transactions
	Route::get('hr/get_activity_in_network_transactions', 'EclaimController@getActivityInNetworkTransactions');
	Route::get('hr/get_activity_out_network_transactions', 'EclaimController@getActivityOutNetworkTransactions');
	// update dependent details
	Route::post("hr/update_dependent_details", 'DependentController@updateDependentDetails');

	// create dependent account
	Route::post('hr/create_dependent_accounts', 'DependentController@createDependentAccount');
	// get employee dependents
	Route::get('hr/get_employee_dependents', 'DependentController@getEmployeeDependents');
	
	// create replacement employee seat
	Route::post('hr/create_employee_replace_seat', 'BenefitsDashboardController@createEmployeeReplacementSeat');
	// get employee refund status plan
	Route::get('hr/get_employee_refund_status_type', 'BenefitsDashboardController@checkEmployeePlanRefundType');
	// enroll new employee vacan seat
	Route::post('hr/enroll_employee', 'BenefitsDashboardController@enrolleEmployeeSeat');

	// dependent withdraw
	Route::post('hr/with_draw_dependent', 'BenefitsDashboardController@withDrawDependent');
	// replace dependent
	Route::post('hr/replace_new_dependent', 'DependentController@replaceDependent');
	// create vacant seat dependent
	Route::post('hr/create_dependent_replace_seat', 'DependentController@createDependentVacantSeat');
	// check employee vacant seat replacement
	Route::get('hr/check_employee_vacant_seat', 'BenefitsDashboardController@checkVacantEmployeeSeat');
	Route::get('hr/check_dependent_vacant_seat', 'DependentController@checkVacantDependentSeat');
	// enroll dependent vacant
	Route::post('hr/enroll_dependent_vacant', 'DependentController@enrollDependent');
	// remove tier api
	Route::post('hr/remove_plan_tier', 'PlanTierController@removeTier');
	// get employee plan covers
	Route::get('hr/get_employee_plan_covers', 'BenefitsDashboardController@getEmployeePlanInformation');
	// download dependent invoice
	// create plan tier
	Route::post("hr/create/plan_tier", "PlanTierController@createPlanTier");
	// reset employee account
	Route::post("hr/employee_reset_account", 'BenefitsDashboardController@employeeResetAccount');
	// download in-network-transactions
	Route::get('hr/download_in_network_transactions', 'BenefitsDashboardController@downloadInNetwork');
	// revert to pending e-claim
	Route::post('hr/revert_pending_e_claim', 'EclaimController@revertPending');
	// check out-of-network transaction duplicates
	Route::get('hr/check_duplicate_transaction', 'EclaimController@checkOutofNetwork');
	// get employee spending account summary
	Route::get('hr/get_employee_spending_account_summary', 'BenefitsDashboardController@getEmployeeSpendingAccountSummaryNew');
	// upload e-claim receipt
	Route::post('hr/upload_e_claim_receipt', 'EclaimController@uploadOutOfNetworkReceipt');
	// update cap per visit of employee
	Route::post('hr/update_employee_cap', 'EmployeeController@updateCapPerVisitEmployee');
	// get pre signed e-claim doc
	Route::get('hr/get_e_claim_doc', 'EclaimController@getPresignedEclaimDoc');
	
	// get account type
	Route::get('hr/get_company_account_type', 'BenefitsDashboardController@getCompanyPlanAccountType');
	// get clinic type lists for company block
	Route::get('hr/get_block_clinic_type_lists_status', 'EmployeeController@getBlockClinicTypeLists');
	// get clinic type lists for employee block
	Route::get('hr/get_employee_block_clinic_type_lists_status', 'EmployeeController@getBlockClinicTypeListsEmployee');
	// route get clinic lists for compay block
	Route::get('hr/get_company_block_lists', 'EmployeeController@getCompanyBlockClinicLists');
	// route get clinic lists for emplouee block
	Route::get('hr/get_employee_company_block_lists', 'EmployeeController@getCompanyBlockClinicListsEmployee');
	// route get clnic active lists company block
	Route::get('hr/get_clinic_lists_block_company', 'EmployeeController@getCompanyActiveClinicLists');
	// route get clnic active lists company block
	Route::get('hr/get_employee_clinic_lists_block_company', 'EmployeeController@getCompanyActiveClinicListsEmployee');
	// create company block
	Route::post('hr/create_company_block_lists', 'EmployeeController@createCompanyBlockClinicLists');
	// create employee block
	Route::post('hr/create_employee_company_block_lists', 'EmployeeController@createCompanyBlockClinicListsEmployee');
	// get employee cap per visits
	Route::get('hr/employee_cap_per_visit_list', 'EmployeeController@employeeCapPerVisit');
	// upload employee cap per visit
	Route::post('hr/upload_employee_cap_per_visit', 'EmployeeController@uploadCaperPervisit');
	// get customer spending account status
	Route::get('hr/get_spending_account_status', 'PlanRenewalController@getEntitlementEnrolmentStatus');
	// get member entitlement
	Route::get('hr/get_member_entitlement', 'EmployeeController@getMemberEntitlement');
	// calculate pro ration
	Route::post('hr/get_member_entitlement_calculation', 'EmployeeController@calculateProRation');
	// get entitlement status
	Route::get('hr/get_member_new_entitlement_status', 'EmployeeController@entitlementStatus');
	// create new entitlement
	Route::post('hr/create_member_new_entitlement', 'EmployeeController@createNewEntitlement');
	// get hr date terms
	Route::get('hr/get_date_terms', 'CorporateController@getCompanyDateTerms');
	// get customer spending account status
	Route::get('hr/spending_account_status', 'BenefitsDashboardController@spendingAccountStatus');
	// get excel link
	Route::get('hr/get_excel_link', 'BenefitsDashboardController@getExcelLink');
	// route get employee lists for bulk allocation
	Route::get('hr/get_employee_lists_bulk_allocation', 'BenefitsDashboardController@getEmployeeListsBulk');
	// upload employee allocation bulk
	Route::post('hr/upload_employee_bulk_allocation', 'EmployeeController@uploadEmployeeBulkAllocation');
	// get member credits
	Route::get('hr/member_credits', 'EmployeeController@getMemberCreditDetails');
	// create new allocation
	Route::post('hr/create_member_credits_allocation', 'EmployeeController@createNewAllocation');
	// get spending invoice purchse
	Route::get('hr/get_spending_invoice_purchase_lists', 'BenefitsDashboardController@getSpendingInvoicePurchaseLists');
	// update company HR details / employee enrollment
	Route::post('hr/update_company_hr_details', 'CorporateController@updateCompanyHrDetails');
	// EMPLOYEE ENROLLMENT V2 ROUTES
	// get plan details
	Route::get('hr/get_plan_details', 'BenefitsDashboardController@getPlanDetails');
	// get enrollment histories
	Route::get('hr/get_plan_enrollment_histories', 'BenefitsDashboardController@getEnrollmentHistories');
	// get invoice histories
	Route::get('hr/get_plan_invoice_histories', 'BenefitsDashboardController@getInvoiceHistories');
	// get employee enrollment status
	Route::get('hr/get_employee_enrollment_status', 'EmployeeController@getEmployeeEnrollmentStatus');
	// check fields for replacement
	Route::post('hr/check_user_field_replacement', 'EmployeeController@checkMemberReplaceDetails');
	// update edit schedule
	Route::post('hr/update_enrollment_schedule', 'BenefitsDashboardController@updateEnrollmentSchedule');
	// seend activaton email
	Route::post('hr/send_immediate_activation', 'EmployeeController@SendMemberActivation');
	// list og old plans
	Route::get('hr/get_old_list_plans', 'BenefitsDashboardController@getOldPlansLists');
	// hr send email account spending inquiry
	Route::post('hr/send_spending_activation_inquiry', 'BenefitsDashboardController@sendSpendingActivateInquiry');
	// update plan details
	Route::post('hr/update_employee_active_plan_details', 'BenefitsDashboardController@updateActivePlanDetails');
	// update dependent details
	Route::post('hr/update_dependent_active_plan_details', 'BenefitsDashboardController@updateActiveDependentDetails');
	Route::get('hr/get_users_by_active_plan', 'BenefitsDashboardController@enrolledUsersFromActivePlan');
	// get employee refund details
	Route::post('hr/get_member_refund_calculation', 'EmployeeController@getRefundEmployeeSummary');
	// get member allocation activity
	Route::get('hr/get_member_allocation_activity', 'SpendingAccountController@getMemberAllocationActivity');
	// get mednefits credits account
	Route::get('hr/get_mednefits_credits_account', 'SpendingAccountController@getMednefitsCreditsAccount');
	// get company wallet details
	Route::get('hr/get_member_wallet_details', 'SpendingAccountController@getMemberWalletDetails');
	// get company date terms
	Route::get('hr/get_company_date_terms', 'SpendingAccountController@getTermsSpendingDates');
	Route::get('hr/spending_account_activity', 'SpendingAccountController@spendingAccountActivities');
	// get bebnefits coverage details
	Route::get('hr/get_benefits_coverage_details', 'SpendingAccountController@getBenefitsCoverageDetails');
	// get company medical wallet details
	Route::get('hr/get_company_wallet_details', 'SpendingAccountController@getWalletDetails');
	// update wallet details
	Route::post('hr/update_member_wallet_details', 'SpendingAccountController@updateWalletDetails');
	// activate wellness wallet
	Route::post('hr/activate_wellness_wallet_details', 'SpendingAccountController@activeWellnessWallet');
	// update spending payment method
	Route::post('hr/update_spending_payment_method', 'SpendingAccountController@updateSpendingPaymentMethod');
	// create top up mednefits credits
	Route::post('hr/create_top_up_mednefits_credits', 'SpendingAccountController@createMednefitsCreditsTopUp');
	// activate mednefis basic plan
	Route::post('hr/activate_mednefits_basic_plan', 'SpendingAccountController@activateBasicPlan');
	// wallet activation or deactivation
	Route::post('hr/wallet_activate_deactivate', 'SpendingAccountController@activateDeactivateWallet');
	// enable disable mednefits credits account
	Route::post('hr/enabled_disabled_mednefits_credits_account', 'SpendingAccountController@enableDisableCreditsAccount');
	// activate company mednefits credits
	Route::post('hr/activate_company_mednefits_credits', 'SpendingAccountController@activateMednefitCreditsAccount');
	
	// GOD'S VIEW ROUTE
	//get corporate linked account
	Route::get('hr/get/corporate_linked_account', 'CorporateController@getCorporateLinkedAccount');
	// create unlinked account
	Route::post('hr/unlink/company_account', 'CorporateController@unlinkCompanyAccount');

	// login company link account
	Route::get('hr/login_company_linked', 'BenefitsDashboardController@accessCompanyLogin');
	// get refund invoice
	Route::get('hr/get_refund_invoices', 'InvoiceController@getListCompanyPlanWithdrawal');
	//empployee list for update administrator HR
	Route::get('hr/get_employee_list', 'BenefitsDashboardController@getEmployeeCorporate');
	Route::get('hr/validate_employee_name', 'BenefitsDashboardController@validateEmployeeName');

	// get account permission lists
	Route::get('hr/get_account_permissions', 'HrController@getAccountPermissions');
});
	
	Route::get('hr/company_invoice_history', 'SpendingInvoiceController@getCompanyInvoiceHistory');
	Route::get('hr/download_pre_paid_invoice', 'SpendingAccountController@downloadPrepaidInvoice');
	// download non-panel reimbursement
	Route::get('hr/download_non_panel_reimbursement_transactions', 'EclaimController@downloadNonPanelReimbursement');
	// download non-panel invoice
	Route::get('hr/download_non_panel_invoice', 'EclaimController@downloadNonPanelInvoice');
	// downloand plan invoice
	Route::get('hr/plan_all_download', 'BenefitsDashboardController@downloadPlanInvoice');
	// get company employees and credits left
	Route::get('hr/get_company_employee_lists_credits', 'BenefitsDashboardController@newGetCompanyEmployeeWithCredits');
	
	Route::get('hr/download_bulk_allocation_employee_lists', 'EmployeeController@downloadEmployeeBulkLists');
	// download spending invoice details
	Route::get('hr/download_spending_purchase_invoice', 'SpendingAccountController@downloadPrepaidInvoice');

// download employee cap per visit
Route::get('hr/download_out_of_network_csv', 'EclaimController@downloadEclaimCsv');
Route::get('hr/download_employee_cap_per_visit', 'EmployeeController@downloadCaperPervisitCSV');

// intro login for clinic
Route::get('provider-portal-login', 'HomeController@clinicLogin');
Route::get('app/clinic/login', 'HomeController@clinicLogin');
// main login pagef
Route::get('app/login', 'HomeController@introPageLogin');



// SPENDING ACCOUNT LANDING PAGE
Route::get('/sa-landing', 'HomeController@getSALandingPageView');
Route::get('/enquiry-form', 'HomeController@getEnquiryFormView');



// welcome pack
Route::get('get/welcome-pack-corporate', 'HomeController@welcomePackCorporate');
Route::get('get/welcome-pack-individual/{id}', 'HomeController@welcomePackIndividual');
Route::get('get/contract/{id}', 'HomeController@getContract');

// invoice for corporate
Route::get('get/invoice/{id}', 'InvoiceController@corporateInvoice');
Route::get('get/certificate/{id}', 'InvoiceController@getCertificate');
Route::get('get/receipt', 'InvoiceController@getReceipt');
Route::get('get/statement/{id}', 'InvoiceController@getHrStatement');
// get stripe token
Route::get('app/get/token', 'CarePlanPurchaseController@getToken');

// Route::get('test/payout', 'PaymentsController@testPayout');
Route::get('conclude/page/{id}', 'HomeController@concludePage');

// send try-three-months
Route::post('send/try-three-months', 'HomeController@sendTryThreeMonths');

Route::get('conclude/page/{id}/{rate}', 'HomeController@concludePage');

Route::post('app/save/clinic/rating', 'HomeController@saveClinicRating');

Route::get('get_previous_bookings', 'UserWebController@getPreviousBookings');

// care plan purchase route
Route::get('get/resume/purchase', 'CarePlanPurchaseController@resumeCarePlanPurchase');
Route::get('get/purchase/data/{id}', 'CarePlanPurchaseController@getCarePlanPurchase');
Route::post('insert/purchase', 'CarePlanPurchaseController@insertCarePlanBusiness');
Route::post('insert/corporate_business_information', 'CarePlanPurchaseController@insertCarePlanBusinessInformation');
Route::post('insert/corporate_plan', 'CarePlanPurchaseController@insertCorporatePlan');
Route::post('insert/corporate_business_contact', 'CarePlanPurchaseController@insertCorporateBusinessContact');
Route::post('insert/hr_dashboard_account', 'CarePlanPurchaseController@createHRDashboardAccount');
Route::post('insert/corporate_choose_payment', 'CarePlanPurchaseController@insertPayment');
Route::post('payment/insert/corporate_credit_payment', 'CarePlanPurchaseController@payCreditCardPlan');
Route::post('insert/corporate_promo_code', 'CarePlanPurchaseController@corporateMatchCode');

// individual
Route::post('insert/customer/personal_details','CarePlanPurchaseController@insertCustomerPersonalDetails');

// update endpoints for care plan purchases
Route::post('update/purchase_corporate_start', 'CarePlanPurchaseController@updateCorporateBuyStart');
Route::post('update/purchase_corporate_plan', 'CarePlanPurchaseController@updateCorporatePlan');
Route::post('update/purchase_corporate_business_information', 'CarePlanPurchaseController@updateBusinessInformation');

Route::get('/uat/care-plan', 'CarePlanController@index');
Route::get('app/get_existing_appointments/{id}', 'CalendarController@getExistingAppointments');

// statement of account
Route::get('app/clinic/statement/{id}', 'InvoiceController@getClinicStatement');
Route::get('app/clinic/print_statement/{id}', 'InvoiceController@downloadClinicStatementPDF');
Route::post('app/clinic/statement_list', 'InvoiceController@getClinicStatementList');


// for corporate booking
Route::get('app/corporate/get-doctor/{id}', 'DoctorWidgetController@getDoctorList');
Route::post('app/corporate/load-end-time','DoctorWidgetController@loadEndTime');
Route::post('app/corporate/load-procedure-data','DoctorWidgetController@loadProcedureData');
Route::post('app/corporate/book','DoctorWidgetController@newBooking');
Route::post('app/corporate/enable-dates','DoctorWidgetController@enableDates');
Route::post('app/corporate/disable-times','DoctorWidgetController@disableTimes');

Route::get('app/corporate/get_corporate/{id}', 'CorporateController@getCorporateById');
Route::post('app/corporate/search', 'CorporateController@searchCoporate');
Route::get('app/corporate/get_doctors', 'CorporateController@getDoctors');
Route::get('app/corporate/get_doctor_procedures/{id}', 'CorporateController@getDoctorProcedure');

// landing page

// Route::get('/', 'HomeController@index');
// Route::get('/index', 'HomeController@index');
// Route::get('/health-professionals', 'HomeController@healthProfessionals');
// Route::get('/corporate', 'HomeController@corporate');
// Route::get('/privacy-policy', 'HomeController@privacy');
// Route::get('/promo', 'HomeController@promo');
// Route::get('/terms', 'HomeController@terms');
// Route::get('/get_quote', 'HomeController@quote');


// Route::get('/', 'HomeController@temp_index');
Route::get('/', 'HomeController@introPageLogin');
// Route::get('/employers', 'HomeController@temp_index');
// Route::get('/individuals', 'HomeController@individual');
// Route::get('/health-partner', 'HomeController@health_partner');
// Route::get('/our-story', 'HomeController@our_story');
// Route::get('/get-mednefits', 'HomeController@get_mednefits');

// Route::get('/provider-terms', 'HomeController@provider_terms');
// Route::get('/user-terms', 'HomeController@user_terms');
// Route::get('/privacy', 'HomeController@privacy_policy');
// Route::get('/insurance-license', 'HomeController@insurance_license');

// Route::get('/buy-insurance', 'HomeController@buy_insurance');
// Route::get('/bonus-credits', 'HomeController@bonus_credits');
// Route::get('/health-benefits', 'HomeController@health_benefits');
// Route::get('/our-health-partners', 'HomeController@our_health_partners');

// Route::get('/how-it-works', 'HomeController@how_it_works');

// Route::get('/outpatient-care', 'HomeController@outpatient_care');
// Route::get('/hospital-care', 'HomeController@hospital_care');
// Route::get('/try-three-months', 'HomeController@try_three_months');
// Route::get('/mednefits-care-plan', 'HomeController@mednefits_care_plan');
// Route::get('/mednefits-employer', 'HomeController@mednefits_employer');
// Route::get('/mednefits-care-bundle-corporate-insurance', 'HomeController@mednefits_care_bundle_corporate');

Route::post('app/contact','HomeController@contactMedicloud');
Route::post('app/subscribe','HomeController@subscribeMedicloud');
Route::get('/user','HomeController@userView');

// activate corporate account
Route::get('app/corporate/activate/{id}', 'CorporateController@activateAccount');

// activate user wallet account
Route::get('app/activate/user/{id}', 'TopUpPassController@activateUserAccount');
Route::post('app/update/user/account/activate', 'TopUpPassController@updateUserAccountActive');

Route::get('test','testcontroller@index');
Route::get('gcal/insertEvent','GoogleCalenderController@insertEvent');
Route::get('app/gcal/sendOAuthRequest','GoogleCalenderController@sendOAuthRequest');
Route::get('app/gcal/getClientToken','GoogleCalenderController@getClientToken');
Route::get('app/gcal/google_calendar_sync/{code}','GoogleCalenderController@getGoogleCodeLink');


Route::post('app/gcal/sendOAuthRequest','GoogleCalenderController@sendOAuthRequest');
Route::post('app/gcal/revokeToken','GoogleCalenderController@revokeToken');
Route::post('app/gcal/loadTokendGmail','GoogleCalenderController@loadTokendGmail');
Route::post('app/gcal/checkUniqueGmail','GoogleCalenderController@checkUniqueGmail');

////////// nhr   2016-2-16  for widget
// widget for multiple clinic view
Route::get('app/widget/multiple/{clinic_name}','DoctorWidgetController@loadClinicData');

Route::get('app/widget/{id}','DoctorWidgetController@index');
Route::post('app/widget/check_nric','DoctorWidgetController@checkNric');
Route::post('app/widget/load-doctor-procedure','DoctorWidgetController@loadDoctorProcedure');
Route::post('app/widget/load-procedure-doctor','DoctorWidgetController@loadProcedureDoctor');
Route::post('app/widget/load-end-time','DoctorWidgetController@loadEndTime');
Route::post('app/widget/load-procedure-data','DoctorWidgetController@loadProcedureData');
Route::post('app/widget/new-widget-booking','DoctorWidgetController@newBooking');
Route::post('app/widget/enable-dates','DoctorWidgetController@enableDates');
Route::post('app/widget/disable-times','DoctorWidgetController@disableTimes');
Route::post('app/widget/send-otp-sms','DoctorWidgetController@sendOtpSms');
Route::post('app/widget/validate-otp','DoctorWidgetController@validateOtp');


// get all schdules from doctors clinic
Route::post('app/calendar/clinic/doctor/schdules','CalendarController@getDoctorsSchedEvents');


////////////////calender    nhr     2016-3-14////////////

Route::post('app/calendar/getdoctorListWithData','CalendarController@getdoctorListWithData');
Route::post('app/calendar/getevent','CalendarController@getEvents');
Route::post('app/calendar/getGoogleEvent','CalendarController@getGoogleEvents');
Route::post('app/calendar/getDoctorProcedure','CalendarController@getDoctorProcedure');
Route::post('app/calendar/load-procedure-details','CalendarController@getProcedureDetails');
Route::post('app/calendar/saveAppointment','CalendarController@saveAppointment');
Route::post('app/calendar/updateAppointment','CalendarController@updateAppointment');
Route::post('app/calendar/updateOnDrag','CalendarController@updateOnDrag');
Route::post('app/calendar/updateOnBlockerDrag','CalendarController@updateOnBlockerDrag');
Route::post('app/calendar/saveBlocker','CalendarController@saveBlocker');
Route::post('app/calendar/blockUnavailable','CalendarController@blockUnavailable');
Route::post('app/calendar/load-users','CalendarController@getAllUsers');
Route::post('app/calendar/getAppointmentDetails','CalendarController@getAppointmentDetails');
Route::post('app/calendar/getExtraEventDetails','CalendarController@getExtraEventDetails');
Route::post('app/calendar/deleteBlockerDetails','CalendarController@deleteBlockerDetails');
Route::post('app/calendar/deleteAppointmentDetails','CalendarController@deleteAppointmentDetails');
Route::post('app/calendar/concludedAppointment','CalendarController@concludedAppointment');
Route::post('app/calendar/No-ShowAppointment','CalendarController@NoShowAppointment');



Route::post('app/calendar/getClinicDetails','CalendarController@getClinicDetails');
Route::post('app/calendar/validatePin','CalendarController@validatePin');
Route::post('app/calendar/getClinicPinStatus','CalendarController@getClinicPinStatus');
Route::post('app/calendar/loadAppointmentCount','CalendarController@loadAppointmentCount');

// Mobile Exercise
Route::get('app/update_user_id_web','HomeController@getMobileExercise');

// ----------------------------------- Settings pages ----------------------------------- //

Route::get('app/setting/main-setting','HomeController@MainSettingsPage');
Route::get('app/setting/claim-report','HomeController@claimReportPage');
Route::post('app/setting/claim-report/api','HomeController@claimReportPageApi');
Route::post('app/setting/service/saveServices','serviceController@saveServices');
Route::post('app/setting/service/deleteServices','serviceController@deleteServices');

Route::post('app/setting/service/update-service-Alldoctor','serviceController@UpdateDoctorAllService');



// .............load ajax pages nhr 22/4/2016
Route::get('app/setting/ajaxGetAccountPage','HomeController@ajaxGetAccountPage');
Route::get('app/setting/ajaxGetStaffPage','HomeController@ajaxGetStaffPage');
Route::get('app/setting/ajaxGetServicPage','HomeController@ajaxGetServicPage');
Route::get('app/setting/ajaxGetNotifyPage','HomeController@ajaxGetNotifyPage');
Route::get('app/setting/ajaxGetProfilePage','HomeController@ajaxGetProfilePage');
Route::get('app/setting/ajaxGetPaymentPage','HomeController@ajaxGetPaymentPage');
Route::post('app/setting/service/ajaxGetEditPage','serviceController@ajaxGetEditPage');

// update service position from sortable process
Route::post('app/setting/service/saveServicePosition','serviceController@saveServicePosition');

Route::get('app/setting/staff/ajaxGetDoctorDetailtabPanel','staffController@ajaxGetDoctorSettingTab');
Route::post('app/setting/staff/ajaxGetStaffDetailtabPanel','staffController@ajaxGetStaffSettingTab');

Route::post('app/setting/staff/ajaxGetStaffDetailsTab','staffController@ajaxGetStaffDetailsTab');
Route::post('app/setting/staff/ajaxGetStaffServicesTab','staffController@ajaxGetStaffServicesTab');
Route::post('app/setting/staff/ajaxGetStaffWorkingHoursTab','staffController@ajaxGetStaffWorkingHoursTab');
Route::post('app/setting/staff/ajaxGetStaffBreaksTab','staffController@ajaxGetStaffBreaksTab');
Route::post('app/setting/staff/ajaxGetStaffTimeOffTab','staffController@ajaxGetStaffTimeOffTab');

Route::post('app/setting/staff/addStaff','staffController@addStaff');
Route::post('app/setting/staff/addDoctor','staffController@addDoctor');
Route::post('app/setting/staff/updateDoctor','staffController@updateDoctor');
Route::post('app/setting/staff/updateStaff','staffController@updateStaff');

Route::post('app/setting/staff/addBreak','staffController@addBreak');
Route::post('app/setting/staff/removeBreak','staffController@removeBreak');
Route::post('app/setting/staff/updateBreak','staffController@updateBreak');

Route::post('app/setting/staff/update-Staff-Doctor-Services','staffController@UpdateDoctorService');
Route::post('app/setting/staff/update-Staff-Doctor-AllServices','staffController@UpdateDoctorAllService');

Route::post('app/setting/staff/updateWorkingHours','staffController@UpdateWorkingHours');
Route::post('app/setting/staff/updateWorkingHoursStatus','staffController@updateWorkingHoursStatus');

Route::post('app/setting/staff/Add-doctor-time-off','staffController@AddDoctorTimeOff');
Route::post('app/setting/staff/get-doctor-time-off','staffController@GetDoctorTimeOff');
Route::post('app/setting/staff/Update-doctor-time-off','staffController@UpdateDoctorTimeOff');
Route::post('app/setting/staff/Delete-doctor-time-off','staffController@DeleteDoctorTimeOff');

Route::post('app/setting/staff/Update-doctor-detail-toggal','staffController@UpdateDetailToggal');
Route::post('app/setting/staff/Update-staff-detail-toggal','staffController@UpdateStaffToggal');

Route::post('app/setting/staff/Delete-doctor','staffController@DeleteDoctorDetail');
Route::post('app/setting/staff/Delete-staff','staffController@DeleteStaffDetail');



Route::post('app/setting/profile/ajaxGetClinicDetailPanel','ProfileController@ajaxGetClinicDetailPanel');
Route::post('app/setting/profile/ajaxGetBusinessHoursPanel','ProfileController@ajaxGetBusinessHoursPanel');
Route::post('app/setting/profile/ajaxGetclinicPasswordPanel','ProfileController@ajaxGetclinicPasswordPanel');

Route::post('app/setting/profile/ajaxGetPaymentDetails','ProfileController@ajaxGetPaymentDetails');

Route::post('app/setting/profile/ajaxGetWebsitePanel','ProfileController@ajaxGetWebsitePanel');
Route::post('app/setting/profile/ajaxGetSocialPlugPanel','ProfileController@ajaxGetSocialPlugPanel');

Route::post('app/setting/profile/ajaxGetClinicHoursTab','ProfileController@ajaxGetClinicHoursTab');
Route::post('app/setting/profile/ajaxGetClinicBreaksTab','ProfileController@ajaxGetClinicBreaksTab');
Route::post('app/setting/profile/ajaxGetClinicTimeOffTab','ProfileController@ajaxGetClinicTimeOffTab');

Route::post('app/setting/profile/Update-Clinic-Details','ProfileController@UpdateClinicDetails');

Route::post('app/setting/profile/Add-Clinic-Breaks','ProfileController@addClinicBreak');
Route::post('app/setting/profile/Update-Clinic-Breaks','ProfileController@updateClinicBreak');
Route::post('app/setting/profile/Remove-Clinic-Breaks','ProfileController@removeClinicBreak');

Route::post('app/setting/profile/Add-Clinic-Time-Off','ProfileController@AddClinicTimeOff');

Route::post('app/setting/profile/Update-Clinic-Password','ProfileController@UpdateClinicPassword');


Route::post('app/setting/payments/history','PaymentsController@getPaymentHistory');
Route::post('app/setting/payments/invoice','PaymentsController@getPaymentInvoice');
Route::post('app/setting/payments/statement','PaymentsController@getPaymentStatement');


// ----------------------- config window functions --------------------

Route::post('app/calendar/load-clinic-details','HomeController@loadClinicDetails');
Route::post('app/calendar/save-clinic-details','HomeController@saveClinicDetails');
Route::post('app/calendar/updateClinicWorkingHours','HomeController@updateClinicWorkingHours');
Route::post('app/calendar/load-clinic-doctor-details','HomeController@loadDoctorDetails');
Route::post('app/calendar/Add-clinic-doctor-details','HomeController@addDoctorDetails');
Route::post('app/calendar/load-clinic-service-details','HomeController@loadServiceDetails');
Route::post('app/calendar/save-clinic-services','HomeController@saveClinicService');
Route::post('app/calendar/delete-clinic-services','HomeController@DeleteClinicService');


// -----------------------------------------------------------------------


Route::post('app/setting/account/update-calendar-config','HomeController@updateClinicDetails');
Route::post('app/send_custom_sms','HomeController@sendCustomSms');

Route::group(array('prefix' => 'v1'), function()
{

	Route::group(array('after' => 'auth.headers'),function(){
		// Route::post('auth/signup', 'Api_V1_AuthController@Signup');
	  	Route::post('auth/login','Api_V1_AuthController@Login');
	    //Route::post('auth/login','Api_V1_AuthController@login');
	    Route::post('auth/forgotpassword','Api_V1_AuthController@Forgot_Password');
	    Route::post('auth/checkemail','Api_V1_AuthController@Check_Email');
	    Route::post('auth/reset-details', 'Api_V1_AuthController@ResetPasswordDetails');
	    Route::post('auth/reset-process', 'Api_V1_AuthController@ProcessResetPassword');

	 	Route::group(array('before' => 'auth.v1'),function(){
	 		// test one tap login
		   	Route::post('auth/one_tap/login', 'Api_V1_AuthController@oneTapLogin');
		    // reset details

		    Route::post('auth/newallergy','Api_V1_AuthController@AddNewAllergy');
		    Route::post('auth/newcondition','Api_V1_AuthController@AddNewMedicalCondition');
		    Route::post('auth/newmedication','Api_V1_AuthController@AddNewUserMedication');
		    Route::post('auth/newhistory','Api_V1_AuthController@AddNewMedicalHistory');
		    Route::post('auth/update','Api_V1_AuthController@UpdateUserProfile');
		    Route::post('auth/updatehistory','Api_V1_AuthController@UpdateMedicalHistory');
		    Route::post('auth/change-password','Api_V1_AuthController@ChangePassword');
		    Route::post('auth/device-token','Api_V1_AuthController@AddDeviceToken');
		    Route::post('auth/disable-profile','Api_V1_AuthController@DisableProfile');
		    Route::post('auth/otpupdate','Api_V1_AuthController@OTPProfileUpdate');
		    Route::post('auth/otpvalidation','Api_V1_AuthController@OTPCodeValidation');
		    Route::post('auth/otpresend','Api_V1_AuthController@OTPCodeResend');

		    // create or update pin
		    Route::post('auth/pin', 'Api_V1_AuthController@pin');
		    Route::post('auth/update_pin', 'Api_V1_AuthController@updatePin');

		    //for get
		    Route::get('auth/logout','Api_V1_AuthController@LogOut');
		    Route::get('auth/coordinate','Api_V1_AuthController@FindCoordinate');
		    Route::get('auth/userprofile','Api_V1_AuthController@UserProfile');
		    Route::get('auth/e_card_details','Api_V1_AuthController@getEcardDetails');
		    Route::get('auth/deleteallergy','Api_V1_AuthController@DeleteUserAllergy');
		    Route::get('auth/deletecondition','Api_V1_AuthController@DeleteUserCondition');
		    Route::get('auth/deletemedication','Api_V1_AuthController@DeleteUserMedication');
		    Route::get('auth/deletehistory','Api_V1_AuthController@DeleteMedicalHistory');


		    //Route::get('auth/create','Api_V1_AuthController@create');
		    //        array('names' => array('create' => 'photo.build')));


		    //clinic
		    Route::get('clinic/search','Api_V1_ClinicController@Search');
		    //Route::get('clinic/clinicdetails','Api_V1_ClinicController@ClinicDetails');
		    Route::get('clinic/clinicdetails/{id}','Api_V1_ClinicController@ClinicDetails');
		  	Route::get('clinic/nearby','Api_V1_ClinicController@Nearby');

		  	Route::get('clinic/new_nearby','Api_V1_ClinicController@NewNearby');

		    Route::get('clinic/booking-history','Api_V1_ClinicController@AppointmentHistory');
		    //Route::get('clinic/booking-detail','Api_V1_ClinicController@AppointmentDetails');
		    Route::get('clinic/booking-detail/{id}','Api_V1_ClinicController@AppointmentDetails');
		    Route::get('clinic/booking-delete','Api_V1_ClinicController@AppointmentDelete');
		    Route::get('clinic/panel-nearby','Api_V1_ClinicController@PanelClinicNearby');
		    Route::get('clinic/appointment','Api_V1_ClinicController@UserAppointmentValidation');
		    Route::get('clinic/procedure_details/{id}','Api_V1_ClinicController@ProcedureDetails');
		    Route::get('clinic/doctor_procedure/{id}','Api_V1_ClinicController@ClinicDoctorProcedures');

		    //Post - Clinics




		    Route::get('clinic/see','Api_V1_ClinicController@see');
		    Route::post('clinic/post','Api_V1_ClinicController@post');

		    //Route::get('doctor/doctordetails','Api_V1_DoctorController@DoctorDetails');
		    //Route::get('doctor/doctor_details/{clinicid}/{doctorid}/{procedureid}','Api_V1_DoctorController@FullDoctorDetails');
		    Route::post('doctor/doctor_details','Api_V1_DoctorController@FullDoctorDetails');
		    Route::post('doctor/slots-refresh','Api_V1_DoctorController@AccessMoreSlots');
		    Route::get('doctor/availability','Api_V1_DoctorController@getDoctorAvailableSlots');

		    Route::get('doctor/test','Api_V1_DoctorController@test');


		    //For doctor
		    Route::post('doctor/booking-queue','Api_V1_DoctorController@QueueBooking');
		    Route::post('doctor/booking-slot','Api_V1_DoctorController@SlotsBooking');
		    //Route::post('doctor/refresh-queue','Api_V1_DoctorController@QueueRefresh');
		    //Route::post('doctor/refresh-slot','Api_V1_DoctorController@SlotRefresh');
		    Route::post('doctor/moreslots','Api_V1_DoctorController@DoctorSlotsForDate');
		    Route::post('doctor/morequeues','Api_V1_DoctorController@DoctorQueueForDate');

		    Route::post('doctor/confirm-queue','Api_V1_DoctorController@ConfirmQueueBooking');
		    Route::post('doctor/confirm-slot','Api_V1_DoctorController@ConfirmSlotBooking');
		    Route::post('doctor/booking-delete','Api_V1_DoctorController@BookingDelete');


		//Route::resource('auth', 'Api_V1_AuthController');

		    //For insurance company
		    //GET
		    Route::get('insurance/company','Api_V1_InsuranceController@getAllInsuranceCompany');
		    Route::get('insurance/policy','Api_V1_InsuranceController@AllUserInsurancePolicy');
		    Route::get('insurance/delete','Api_V1_InsuranceController@DeleteInsurancePolicy');
		    Route::get('insurance/change-primary','Api_V1_InsuranceController@ChangePrimaryPolicy');

		    //POST
		    Route::post('insurance/add_policy','Api_V1_InsuranceController@AddUserInsurancePolicy');



		    // nhr/////////////////////////////////////////////////////////////

		    Route::get('clinic/clinic_type', 'Api_V1_ClinicController@getClnicType');
		    Route::get('new/clinic/clinic_type', 'Api_V1_ClinicController@NewClnicType');

		    Route::get('clinic/clinic_type/sub', 'Api_V1_ClinicController@getClnicTypeSub');
		    Route::get('clinic/clinic_by_type/{id}', 'Api_V1_ClinicController@getClinicByType');
		    Route::get('clinic/main_search','Api_V1_ClinicController@mainSearch');
		    Route::get('clinic/sub_search','Api_V1_ClinicController@subSearch');
		    Route::get('clinic/get_favourite_clinics','Api_V1_ClinicController@getFavouriteClinics');
		    Route::get('clinic/get_promo_message','Api_V1_ClinicController@getpromoMessage');

		    Route::post('clinic/favourite', 'Api_V1_ClinicController@favourite');


		    // get credit details
		    Route::get('user/credits', 'Api_V1_AuthController@GetCredits');
		    Route::post('user/match/promo', 'Api_V1_AuthController@getPromoCredit');

		    // backup email
		    Route::post('user/create/backup/email', 'Api_V1_AuthController@createBackUpEmail');
		    // get clinic details from qr code
		    Route::get('clinic/details/{id}', 'Api_V1_AuthController@getNewClinicDetails');
		    // check user pin
		    Route::post('clinic/send_payment', 'Api_V1_AuthController@checkUserPin');
		    // send notification to clinic when customer will pay directly to clinic
		    Route::post('clinic/payment_direct', 'Api_V1_AuthController@notifyClinicDirectPayment');
		    // save photo receipt
		    Route::post('user/save_in_network_receipt', 'Api_V1_AuthController@saveInNetworkReceipt');
		    // save photo bulk
		    Route::post('user/save_receipt_bulk', 'Api_V1_AuthController@saveImageReceiptBulk');
		    // get family coverage sub accounts
		    Route::get('user/family_coverage_user_lists', 'Api_V1_AuthController@getFamilCoverageAccounts');
		    // get wallet settings
		    Route::get('user/wallet_settings', 'Api_V1_AuthController@getWalletSettings');
		    // update or insert wallet setting
		    Route::post('user/set_wallet_settings', 'Api_V1_AuthController@setWalletSettings');
		    // get in-network transaction lists
		    Route::get('user/in_network_transactions', 'Api_V1_AuthController@getNetworkTransactions');
		    // get specific in-network transaction
		    Route::get('user/specific_in_network/{id}', 'Api_V1_AuthController@getInNetworkDetails');
		    // upload receipt e-claim
		    // Route::post('user/upload_out_of_network_receipt', 'Api_V1_AuthController@uploadReceipt');
		    // upload receipt in-network
		    Route::post('user/upload_in_network_receipt', 'Api_V1_AuthController@uploadInNetworkReceipt');
		    // get e-claim transactions
		    Route::get('user/e_claim_transactions', 'Api_V1_AuthController@getEclaimTransactions');
		    Route::get('user/specific_e_claim_transaction/{id}', 'Api_V1_AuthController@getEclaimDetails');
		    // upload out-of-network(e-claim receipt)
		    Route::post("user/save_out_of_network_receipt", 'Api_V1_AuthController@saveEclaimReceipt');
		    // get lists of spending type
		    Route::get("user/health_type_lists", 'Api_V1_AuthController@getHealthLists');
		    // create e-claim transaction
		    Route::post("user/create_e_claim", 'Api_V1_AuthController@createEclaim');
	 	});
	});
});


// version 2 mobile api
Route::group(array('prefix' => 'v2'), function()
{

	Route::group(array('after' => 'auth.headers'),function(){
		// Route::post('auth/signup', 'Api_V1_AuthController@Signup');
	  	Route::post('auth/login','Api_V1_AuthController@Login');
	  	// new login method 
	  	Route::post('auth/new_login','Api_V1_AuthController@newLogin');
	    //Route::post('auth/login','Api_V1_AuthController@login');
	    Route::post('auth/forgotpassword','Api_V1_AuthController@Forgot_PasswordV2');
	    Route::post('auth/checkemail','Api_V1_AuthController@Check_Email');
	    Route::post('auth/reset-details', 'Api_V1_AuthController@ResetPasswordDetails');
	    Route::post('auth/reset-process', 'Api_V1_AuthController@newProcessResetPassword');

		// for getting member lists
		Route::get('member/lists', 'Api_V1_AuthController@getCompanyMemberLists');

		Route::post('auth/reset-process', 'Api_V1_AuthController@newProcessResetPassword');
		
		Route::post('auth/check-member-exist', 'Api_V1_AuthController@checkMemberExist');
		Route::post('auth/send-otp-mobile', 'Api_V1_AuthController@sendOtpMobile');
		Route::post('auth/validate-otp-mobile', 'Api_V1_AuthController@validateOtpMobile');
		Route::put('auth/registerMobileNumber', 'Api_V1_AuthController@registerMobileNumber');
		Route::post('auth/add-postal-code-member', 'Api_V1_AuthController@addPostalCodeMember');
		Route::post('auth/activated-create-new-password', 'Api_V1_AuthController@createNewPasswordByMember');
		
		Route::post('auth/activated-administrator-user', 'Api_V1_AuthController@createNewPasswordByAdministrator');

		// for getting member lists
		Route::get('member/lists', 'Api_V1_AuthController@getCompanyMemberLists');

	 	Route::group(array('before' => 'auth.v2'),function(){
	 		// test one tap login
		   	Route::post('auth/one_tap/login', 'Api_V1_AuthController@oneTapLogin');
		    // reset details

		    Route::post('auth/newallergy','Api_V1_AuthController@AddNewAllergy');
		    Route::post('auth/newcondition','Api_V1_AuthController@AddNewMedicalCondition');
		    Route::post('auth/newmedication','Api_V1_AuthController@AddNewUserMedication');
		    Route::post('auth/newhistory','Api_V1_AuthController@AddNewMedicalHistory');
		    Route::post('auth/update','Api_V1_AuthController@newUpdateUserProfile');
		    Route::post('auth/updatehistory','Api_V1_AuthController@UpdateMedicalHistory');
		    Route::post('auth/change-password','Api_V1_AuthController@ChangePassword');
		    Route::post('auth/device-token','Api_V1_AuthController@AddDeviceToken');
		    Route::post('auth/disable-profile','Api_V1_AuthController@DisableProfile');
		    Route::post('auth/otpupdate','Api_V1_AuthController@OTPProfileUpdate');
		    Route::post('auth/otpvalidation','Api_V1_AuthController@OTPCodeValidation');
		    Route::post('auth/otpresend','Api_V1_AuthController@OTPCodeResend');

		    // create or update pin
		    Route::post('auth/pin', 'Api_V1_AuthController@pin');
		    Route::post('auth/update_pin', 'Api_V1_AuthController@updatePin');

		    //for get
		    Route::get('auth/logout','Api_V1_AuthController@LogOut');
		    Route::get('auth/coordinate','Api_V1_AuthController@FindCoordinate');
		    Route::get('auth/userprofile','Api_V1_AuthController@UserProfile');
		    Route::get('auth/e_card_details','Api_V1_AuthController@getEcardDetails');
		    Route::get('auth/deleteallergy','Api_V1_AuthController@DeleteUserAllergy');
		    Route::get('auth/deletecondition','Api_V1_AuthController@DeleteUserCondition');
		    Route::get('auth/deletemedication','Api_V1_AuthController@DeleteUserMedication');
		    Route::get('auth/deletehistory','Api_V1_AuthController@DeleteMedicalHistory');


		    //Route::get('auth/create','Api_V1_AuthController@create');
		    //        array('names' => array('create' => 'photo.build')));


		    //clinic
		    Route::get('clinic/search','Api_V1_ClinicController@Search');
		    //Route::get('clinic/clinicdetails','Api_V1_ClinicController@ClinicDetails');
		    Route::get('clinic/clinicdetails/{id}','Api_V1_ClinicController@ClinicDetails');
		  	Route::get('clinic/all_nearby','Api_V1_ClinicController@Nearby');

		  	Route::get('clinic/paginate_nearby','Api_V1_ClinicController@NewNearby');

		    Route::get('clinic/booking-history','Api_V1_ClinicController@AppointmentHistory');
		    //Route::get('clinic/booking-detail','Api_V1_ClinicController@AppointmentDetails');
		    Route::get('clinic/booking-detail/{id}','Api_V1_ClinicController@AppointmentDetails');
		    Route::get('clinic/booking-delete','Api_V1_ClinicController@AppointmentDelete');
		    Route::get('clinic/panel-nearby','Api_V1_ClinicController@PanelClinicNearby');
		    Route::get('clinic/appointment','Api_V1_ClinicController@UserAppointmentValidation');
		    Route::get('clinic/procedure_details/{id}','Api_V1_ClinicController@ProcedureDetails');
		    Route::get('clinic/doctor_procedure/{id}','Api_V1_ClinicController@ClinicDoctorProcedures');

		    //Post - Clinics




		    Route::get('clinic/see','Api_V1_ClinicController@see');
		    Route::post('clinic/post','Api_V1_ClinicController@post');

		    //Route::get('doctor/doctordetails','Api_V1_DoctorController@DoctorDetails');
		    //Route::get('doctor/doctor_details/{clinicid}/{doctorid}/{procedureid}','Api_V1_DoctorController@FullDoctorDetails');
		    Route::post('doctor/doctor_details','Api_V1_DoctorController@FullDoctorDetails');
		    Route::post('doctor/slots-refresh','Api_V1_DoctorController@AccessMoreSlots');
		    Route::get('doctor/availability','Api_V1_DoctorController@getDoctorAvailableSlots');

		    Route::get('doctor/test','Api_V1_DoctorController@test');


		    //For doctor
		    Route::post('doctor/booking-queue','Api_V1_DoctorController@QueueBooking');
		    Route::post('doctor/booking-slot','Api_V1_DoctorController@SlotsBooking');
		    //Route::post('doctor/refresh-queue','Api_V1_DoctorController@QueueRefresh');
		    //Route::post('doctor/refresh-slot','Api_V1_DoctorController@SlotRefresh');
		    Route::post('doctor/moreslots','Api_V1_DoctorController@DoctorSlotsForDate');
		    Route::post('doctor/morequeues','Api_V1_DoctorController@DoctorQueueForDate');

		    Route::post('doctor/confirm-queue','Api_V1_DoctorController@ConfirmQueueBooking');
		    Route::post('doctor/confirm-slot','Api_V1_DoctorController@ConfirmSlotBooking');
		    Route::post('doctor/booking-delete','Api_V1_DoctorController@BookingDelete');


			//Route::resource('auth', 'Api_V1_AuthController');

		    //For insurance company
		    //GET
		    Route::get('insurance/company','Api_V1_InsuranceController@getAllInsuranceCompany');
		    Route::get('insurance/policy','Api_V1_InsuranceController@AllUserInsurancePolicy');
		    Route::get('insurance/delete','Api_V1_InsuranceController@DeleteInsurancePolicy');
		    Route::get('insurance/change-primary','Api_V1_InsuranceController@ChangePrimaryPolicy');

		    //POST
		    Route::post('insurance/add_policy','Api_V1_InsuranceController@AddUserInsurancePolicy');



		    // nhr/////////////////////////////////////////////////////////////

		    Route::get('clinic/clinic_type', 'Api_V1_ClinicController@getClnicType');
		    Route::get('new/clinic/clinic_type', 'Api_V1_ClinicController@NewClnicType');

		    Route::get('clinic/clinic_type/sub', 'Api_V1_ClinicController@getClnicTypeSub');
		    Route::get('clinic/clinic_by_type/{id}', 'Api_V1_ClinicController@getClinicByType');
		    Route::get('clinic/main_search','Api_V1_ClinicController@mainSearch');
		    Route::get('clinic/sub_search','Api_V1_ClinicController@subSearch');
		    Route::get('clinic/get_favourite_clinics','Api_V1_ClinicController@getFavouriteClinics');
		    Route::get('clinic/get_promo_message','Api_V1_ClinicController@getpromoMessage');

		    Route::post('clinic/favourite', 'Api_V1_ClinicController@favourite');


		    // get credit details
		    Route::get('user/credits', 'Api_V1_AuthController@getUserWallet');
		    Route::get('member/wallet_details', 'Api_V1_AuthController@getMemberPartialWallet');
		    Route::post('user/match/promo', 'Api_V1_AuthController@getPromoCredit');

		    // backup email
		    Route::post('user/create/backup/email', 'Api_V1_AuthController@createBackUpEmail');
		    // get clinic details from qr code
		    Route::get('clinic/details/{id}', 'Api_V1_AuthController@getNewClinicDetails');
		    // check user pin
		    Route::post('clinic/send_payment', 'Api_V1_TransactionController@payCredits');
		    // Route::post('clinic/create_payment', 'Api_V1_AuthController@payCreditsNew');
		    Route::post('clinic/create_payment', 'Api_V1_TransactionController@payCredits');
		    // send notification to clinic when customer will pay directly to clinic
		    // Route::post('clinic/payment_direct', 'Api_V1_AuthController@notifyClinicDirectPayment');
		    Route::post('clinic/payment_direct', 'Api_V1_TransactionController@notifyClinicDirectPayment');
		    // save photo receipt
		    Route::post('user/save_in_network_receipt', 'Api_V1_AuthController@saveInNetworkReceipt');
		    // save photo bulk
		    Route::post('user/save_receipt_bulk', 'Api_V1_AuthController@saveImageReceiptBulk');
		    // get family coverage sub accounts
		    Route::get('user/family_coverage_user_lists', 'Api_V1_AuthController@getFamilCoverageAccounts');
		    // get wallet settings
		    Route::get('user/wallet_settings', 'Api_V1_AuthController@getWalletSettings');
		    // update or insert wallet setting
		    Route::post('user/set_wallet_settings', 'Api_V1_AuthController@setWalletSettings');
		    // get in-network transaction lists
		    Route::get('user/in_network_transactions', 'Api_V1_TransactionController@getNetworkTransactions');
		    // get specific in-network transaction
		    // Route::get('user/specific_in_network/{id}', 'Api_V1_AuthController@getInNetworkDetails');
		    Route::get('user/specific_in_network/{id}', 'Api_V1_TransactionController@getInNetworkDetails');
		    // upload receipt e-claim
		    // Route::post('user/upload_out_of_network_receipt', 'Api_V1_AuthController@uploadReceipt');
		    // upload receipt in-network
		    Route::post('user/upload_in_network_receipt', 'Api_V1_AuthController@uploadInNetworkReceipt');
		    Route::post('user/upload_in_network_receipt_bulk', 'Api_V1_TransactionController@uploadInNetworkReceiptBulk');
		    // get e-claim transactions
		    Route::get('user/e_claim_transactions', 'Api_V1_AuthController@getEclaimTransactions');
		    Route::get('user/specific_e_claim_transaction/{id}', 'Api_V1_AuthController@getEclaimDetails');
		    // upload out-of-network(e-claim receipt)
		    Route::post("user/save_out_of_network_receipt", 'Api_V1_AuthController@saveEclaimReceipt');
		    // get lists of spending type
		    Route::get("user/health_type_lists", 'Api_V1_AuthController@getHealthLists');
		    // create e-claim transaction
		    Route::post("user/create_e_claim", 'Api_V1_AuthController@createEclaim');
		    // get member list for employee
		    Route::get("user/member_lists", 'Api_V1_AuthController@getFamilCoverageAccounts');
		    // save device token
			Route::post('user/save_device_token', 'PushNotificationController@saveDeviceToken');
			// get currency lists
			Route::get("get/currency_lists", 'Api_V1_AuthController@getCurrencyLists');
			// get app notification message
			Route::get("get/app_update_notification", 'Api_V1_AuthController@getAppUpdateNotification');
			// update notification to read
			Route::post("update/user_notification_read", 'Api_V1_AuthController@updateUserNotification');
			// remove check in data
			Route::post('clinic/cancel_visit', 'Api_V1_AuthController@removeCheckIn');
			// get check_in_id data
			Route::get('get/check_in_data', 'Api_V1_TransactionController@getCheckInData');
			// check e-claim member visit date spending
			Route::post('user/check_e_claim_visit', 'Api_V1_AuthController@checkEclaimVisit');
			// get member dates coverage
			Route::get('user/get_dates_coverage', 'Api_V1_AuthController@getDatesCoverage');
			// create tap ready on boarding
			Route::get('user/ready_on_boarding', 'Api_V1_AuthController@updateReadyOnBoarding');
		 });
		 
		 // get member spending account status feature
		 Route::get('user/get_spending_feature_status', 'Api_V1_AuthController@getMemberAccountSpendingStatus');
	});
});

//Route::group(array('domain' => 'www.tag.loc','prefix' => ''), function()
Route::group(array('prefix' => 'app'), function()
{
    Route::get('auth/getme','App_AuthController@getmenow');
    //xxxxxxxxxxxxx     For Authentication  xxxxxxxxxxxx
    //Auth : GET
    Route::get('auth/register','App_AuthController@CompleteDoctorRegistration');
    Route::get('auth/login','App_AuthController@MainLogin');
    Route::get('auth/logout','App_AuthController@LogOutNow');
    Route::get('auth/forgot','App_AuthController@ForgotPassword');
    Route::get('auth/password-reset','App_AuthController@ResetPassword');
    Route::get('auth/newClinic','App_AuthController@newClinic');

    //Auth : POST
    Route::POST('auth/signup','App_AuthController@MainSignUp');
    Route::POST('auth/loginnow','App_AuthController@ProcessLogin');
    Route::POST('auth/forgot-password','App_AuthController@ProcessForgotPassword');
    Route::POST('auth/resetpassword','App_AuthController@ProcessResetPassword');
    Route::POST('auth/find-user','App_AuthController@FindNricUser');

    // add new clinic nhr
    Route::post('auth/newClinic','Admin_ClinicController@newClinic');

    //This is for testing purpose
    //Route::get('auth/emailtest','App_AuthController@sendEmail');
    Route::get('auth/otptest','App_AuthController@sendOTP');
    Route::get('auth/report','App_AuthController@GetReports');

    //Cron Jobs Implementation
    //Route::get('cron/test','CronController@CronTest');
    Route::get('cron/reminder-today','CronController@RemindAppointment');
    Route::get('cron/reminder-hours','CronController@RemindAppointmentInHours');
    Route::get('cron/reminder-minutes','CronController@RemindAppointmentInMinutes');
    Route::get('cron/diactivate_booking','CronController@DiactivateBookings');

    Route::get('cron/reminder-tomorrow','CronController@SMSAppointmentBeforeDay');
    Route::get('cron/reminder-hour','CronController@SMSAppointmentBeforeHour');
    Route::get('cron/delete-google-event','CronController@deleteGoogleEvent');

    Route::group(array('before' => 'auth.clinic'), function( ){
        //xxxxxxxxxxxxxx    For Clinic Areas    xxxxxxxxxxxxxx
        //Clinic : GET
        //Route::get('clinic/clinic-settings','App_ClinicController@ClinicSettings');
        Route::get('clinic/manage-doctors','App_ClinicController@ManageDoctors');
        Route::get('clinic/new-doctor','App_ClinicController@AddDoctorToClinic');
        Route::get('clinic/dashboard','App_ClinicController@ClinicDashboard');
        Route::get('clinic/booking','App_ClinicController@BookingPage');

        Route::get('clinic/appointment','App_ClinicController@BookNewAppointment');
        Route::get('clinic/settings','App_ClinicController@ClinicSettings');

        Route::get('clinic/settings-dashboard','App_ClinicController@ClinicSettingDashboard');
        Route::get('clinic/dashboard-booking/{id}','App_ClinicController@ClinicDashboardBooking');

        // get clinic details
        Route::get('clinic/clinic_details/{id}','App_ClinicController@ClinicDetails');
        // get clicni details from Session
        Route::get('clinic/details','App_ClinicController@ClinicDetailsFromSession');
        Route::get('clinic/calendar-integration','App_ClinicController@CalendarIntegrationViewPage');
        Route::get('clinic/clinic-details','App_ClinicController@ClinicDetailsPage');
        Route::get('clinic/clinic-procedure','App_ClinicController@ClinicAddProcedurePage');
        Route::get('clinic/clinic-doctor','App_ClinicController@ClinicAddDoctorPage');
        Route::get('clinic/clinic-doctors-view','App_ClinicController@ClinicDoctorsViewPage');
        Route::get('clinic/clinic-doctors-home','App_ClinicController@ClinicDoctorsHomePage');
        Route::get('clinic/update-password-home','App_ClinicController@ClinicUpdatePasswordPage');

        Route::get('clinic/calendar-integration','App_ClinicController@CalendarIntegrationViewPage');
        Route::get('clinic/button-integration','App_ClinicController@buttonIntegrationViewPage');

        Route::get('clinic/opening-times-home','App_ClinicController@ClinicOpeningTimesPage');
        Route::get('clinic/doctor-availability','App_ClinicController@ClinicDoctorAvailabilityPage');
		Route::get('clinic/appointment-home-view','HomeController@showCalender'); //boom
		/*
			Refactor API for gettting providers information for the first time.
		*/
		Route::get('clinic/getProviderBreakHours', 'DashboardController@getProviderBreakHours');
		Route::get('clinic/getProviderOperatingHours', 'DashboardController@getProviderOperatingHours');
		Route::get('clinic/getProvidersDetail', 'DashboardController@getProvidersDetail');
		/* End Here. */
		
		Route::get('clinic/appointment-home-view1','App_ClinicController@ClinicHomeAppointmentPage');
        Route::get('clinic/appointment-doctor-view/{id}','App_ClinicController@SingleDoctorAppointmentPage');

        Route::get('clinic/calendar-view-single','HomeController@showMainCalendarSingleView'); //boom
        Route::get('clinic/calendar-view-group','HomeController@showMainCalendarGroupView'); //boom

        Route::get('clinic/dashboard-summary','HomeController@summaryDashboard'); //boom
        Route::get('clinic/mednefits-tutorials','HomeController@needHelpPage'); //boom


        Route::get('clinic/test','App_ClinicController@test');
        Route::get('clinic/ajax','App_ClinicController@ajax');

        //Clinic : POST
        Route::post('clinic/ajaxpost','App_ClinicController@ajaxpost');
        Route::post('clinic/clinicdata','App_ClinicController@ClinicData');
        Route::post('clinic/load-booking','App_ClinicController@AjaxBookingPage');

        Route::post('clinic/ajax-settings-dashboard','App_ClinicController@ClinicSettingDashboardAJAX');
        Route::post('clinic/ajax-dashboard-booking','App_ClinicController@ClinicDashboardBookingAJAX');

        Route::post('clinic/clinic-image-upload','App_ClinicController@ClinicProfileImageUpload');
        Route::post('clinic/clinic-profile-update','App_ClinicController@ClinicDetailsPageUpdate');

        Route::post('clinic/add-procedure','App_ClinicController@ClinicAddProcedure');
        Route::post('clinic/delete-procedure','App_ClinicController@ClinicDeleteProcedure');
        Route::post('clinic/add-doctor','App_ClinicController@ClinicAddDoctors');
        Route::post('clinic/delete-doctor','App_ClinicController@ClinicDoctorsDelete');
        Route::post('clinic/update-password','App_ClinicController@ClinicPasswordUpdate');
        Route::post('clinic/opening-times','App_ClinicController@ClinicOpeningTimes');
        Route::post('clinic/delete-opening-times','App_ClinicController@ClinicDeleteOpeningTimes');
        Route::post('clinic/clinic-holidays','App_ClinicController@AddClinicHolidays');
        Route::post('clinic/delete-clinic-holiday','App_ClinicController@DeleteClinicHolidays');
        Route::post('clinic/load-doctor-availability','App_ClinicController@LoadClinicDoctorAvailabilityPage');
        Route::post('clinic/availability-times','App_ClinicController@AddDoctorAvailabilityTimes');
        Route::post('clinic/repeat-action','App_ClinicController@RepeatTimeActions');
        Route::post('clinic/open-booking-page','App_ClinicController@OpenBookingPage');
        Route::post('clinic/open-booking-update','App_ClinicController@OpenBookingUpdate');
        Route::post('clinic/load-booking-popup','App_ClinicController@LoadBookingPopup');

        Route::post('clinic/doctor-procedures','App_ClinicController@LoadDoctorProcedures');
        Route::post('clinic/new-appointment','App_ClinicController@NewClinicAppointment');
        Route::post('clinic/update-appointment','App_ClinicController@UpdateClinicAppointment');
        Route::post('clinic/delete-appointment','App_ClinicController@DeleteClinicAppointment');
        Route::post('clinic/conclude-appointment','App_ClinicController@ConcludeClinicAppointment');
        Route::post('clinic/load-appointment-view','App_ClinicController@LoadDoctorsAppointmentView');
        // save appointment from reserve blocker
        Route::post('clinic/save-appointment-reserver', 'App_ClinicController@saveAppointmentFromReserve');
        Route::post('clinic/load-doctors-view','App_ClinicController@LoadDoctorsSelectionView');
        Route::post('clinic/load-singledoctor-view','App_ClinicController@LoadSingleDoctorView');
        Route::post('clinic/change-procedure','App_ClinicController@ChangeProcedures');
        Route::post('clinic/change-startdate','App_ClinicController@ChangeStartDate');
        Route::get('clinic/doctor-update-page/{id}','App_ClinicController@UpdateDoctorPage');
        Route::post('clinic/update-doctor','App_ClinicController@UpdateDoctorDetails');
		Route::post('clinic/channel_update','App_ClinicController@UpdateBookingChannel');
		
		/*****************Clinic : PUT*****************/
		//Refactor API for gettting providers information for the first time.
			Route::put('clinic/updateProvidersDetail', 'DashboardController@updateProvidersDetail');
		/* End Here. */
	   
		
		//Route::get('clinic/','App_ClinicController@index');
        //Route::get('auth/create', 'App_AuthController@create');
    		Route::resource('auth', 'App_AuthController');

        //xxxxxxxxxxx       For Doctor Area         xxxxxxxxxxxxxxxx
        //Doctor    :   POST
        Route::post('doctor/manageslots','App_DoctorController@ManageDoctorSlots');
        Route::post('doctor/newdoctor','App_DoctorController@AddNewDoctor');
        Route::post('doctor/updatedoctor','App_DoctorController@UpdateDoctor');
        Route::post('doctor/manage-slotdetail','App_DoctorController@ManageSlotDetails');
        Route::post('doctor/manage-slotdate','App_DoctorController@ManageSlotsForDate');

        Route::post('doctor/booking-queue','App_DoctorController@QueueSlotBooking');
        Route::post('doctor/delete-appointment','App_DoctorController@DeleteUserAppointment');
        Route::post('doctor/delete-popup','App_DoctorController@OpenDeleteAppointment');
        Route::post('doctor/manage-queue','App_DoctorController@StoppedDoctorQueue');
        Route::post('doctor/start-queue','App_DoctorController@StartedDoctorQueue');
        Route::post('doctor/booking','App_DoctorController@DoctorBooking');
        Route::post('doctor/ajax-booking','App_DoctorController@AjaxDoctorBooking');
        Route::post('doctor/diagnosis','App_DoctorController@DoctorDiagnosis');






        //Doctor    :   GET
        Route::get('doctor/settings','App_DoctorController@DoctorSettings');
        Route::get('doctor/dashboard','App_DoctorController@doctorDashboard');
        Route::get('doctor/home','App_DoctorController@DoctorHome');



            //Route::get('doctor/file-upload','App_DoctorController@DoctorUpload');
            //Route::any('doctor/upload','App_DoctorController@Upload');

        // transaction
        Route::post('clinic/appointment/transaction', 'TransactionController@checkAppointmentTransaction');
        // transaction tests
        Route::post('clinic/transaction/calculate', 'TransactionController@newCalculateTransaction');
        Route::post('clinic/transaction_co_paid/calculate', 'TransactionController@CalculateCoPaidTransaction');
        Route::post('clinic/transaction/finish', 'TransactionController@finishTransaction');

        // dashboard routes
        Route::post('clinic/appointments/count', 'DashboardController@countAppointments');
        Route::get('clinic/appointments/list', 'DashboardController@listAppointments');
        Route::post('clinic/total/revenue', 'DashboardController@getClinicTotalRevenue');
        Route::post('clinic/credits/revenue', 'DashboardController@getClinicCredits');
        Route::post('clinic/collected/revenue', 'DashboardController@getClinicCollected');
        Route::get('clinic/view/appointment/{id}', 'DashboardController@viewAppointment');
        Route::get('clinic/view/transaction/history/limit', 'DashboardController@viewTransactionHistoryLimitView');
        Route::post('clinic/view/schedule/byDate', 'DashboardController@viewScheduleByDate');
        Route::post('clinic/view/transaction/byDate', 'DashboardController@viewTransactionByDate');
        Route::post('clinic/view/payment/transaction/byDate', 'DashboardController@paymentTransactionHistory');
        Route::get('clinic/transaction/payment/download/{start}/{end}', 'DashboardController@paymentDownloadTransactionHistory');
        Route::get('clinic/transaction/payment/search/download/{search}', 'DashboardController@paymentSearchDownloadTransactionHistory');
        Route::get('clinic/get/minimum/date', 'DashboardController@getMinimumDate');

        // invoice
        // payment_bank_details
        Route::post('clinic/update/bank_details', 'UserWebController@createBankDetails');
        Route::get('clinic/bank_details', 'UserWebController@getBankDetails');

        Route::post('clinic/invoice', 'InvoiceController@createInvoice');
        // download invoice
        Route::get('clinic/invoice_download/{id}', 'InvoiceController@downloadInvoice');
        Route::post('clinic/invoice_list_by_date', 'InvoiceController@invoiceListsByDate');
        Route::post('clinic/invoice_list', 'InvoiceController@invoiceLists');
        Route::get('corporate/get_corporate/{id}', 'CorporateController@getCorporateById');
        // Route::post('clinic/newtransaction/calculate', 'TransactionController@newCalculateTransaction');

        // admin
        // Route::get('admin/invoice_list', 'InvoiceController@getAdminInvoice');
        // Route::post('admin/update_payment', 'PaymentsController@updatePaymentRecordPaid');

        // statement of account
        Route::get('clinic/statement/{id}', 'InvoiceController@getClinicStatement');
        Route::post('clinic/statement_list', 'InvoiceController@getClinicStatementList');

        // group events
        Route::post('get/group_events', 'CalendarController@getGroupEvents');
        Route::post('get/group_resources', 'CalendarController@getGroupResource');
        Route::post('check/book_date_resource', 'CalendarController@checkBookDateResource');
        Route::post('reschedule_check/resource', 'CalendarController@rescheduleAppointmentCheck');
        // Route::post('app/reschedule/resource', 'CalendarController@rescheduleAppointment');


        // Route::post('clinic/invoice', 'InvoiceController@createInvoice');
        // Route::post('clinic/invoice_list_by_date', 'InvoiceController@invoiceListsByDate');
        Route::post('clinic/invoice_list', 'InvoiceController@invoiceLists');
        Route::post('corporate/search', 'CorporateController@searchCoporate');
        Route::get('corporate/get_corporate/{id}', 'CorporateController@getCorporateById');
        Route::get('corporate/get_identification_numbers', 'CorporateController@allCorporate');
        Route::post('corporate/add_members', 'CorporateController@addCorporateMembers');

        Route::post('clinic/save/bulk/transaction', 'TransactionController@saveBulkTransaction');
        Route::post('clinic/save/claim/transaction', 'TransactionController@submitClaim');
        Route::get('clinic/get/health_provider/transaction', 'TransactionController@getHealthProviderTransactions');
        Route::get('clinic/get/services', 'HomeController@getServices');

        Route::get('clinic/get/all/users', 'HomeController@getAllUsers');
        Route::get('clinic/search_all_users', 'HomeController@searchUser');
        Route::get('clinic/get/all/special/users', 'HomeController@getAllSpecialUsers');
        Route::get('clinic/get/service/details/{id}', 'HomeController@gerServiceDetails');
        Route::get('clinic/get/user/details/{id}', 'HomeController@gerUserDetails');
        Route::get('clinic/get/corporate/name/{id}', 'HomeController@getCorporateName');
        Route::post('clinic/get/nric_user', 'App_ClinicController@getNRICUser');
        Route::get('clinic/get/special_user/details/{id}', 'App_ClinicController@getUserCareDetails');

        // send email confirmation
        Route::get('clinic/send/book/confirmation/{id}', 'App_ClinicController@sendEmailConfirmation');

        // get backdated claim transactions
        Route::get('clinic/all_transactions', 'TransactionController@getAllTransactions');
        Route::post('clinic/search_by_nric_transactions', 'TransactionController@searchByNricTransactions');
        // Route::get('clinic/remove/backdate_claim_transactions/{id}', 'TransactionController@removeTransactionBackDate');
        Route::get('clinic/remove/transaction', 'TransactionController@removeTransaction');
        Route::post('clinic/update_transaction', 'TransactionController@updateTransactionDetails');

        // qr code
        Route::post('setting/profile/ajaxGetQRPage', 'QRCodeController@viewQRcodes');
        // view check in
        Route::get('clinic/full_screen/check_in/view', 'QRCodeController@viewBigCheckInQR');
        // view payment
        Route::get('clinic/full_screen/payment/view', 'QRCodeController@viewBigPaymentQR');
        // preview mobile claim
        // Route::get('clinic/preview/{id}', 'HomeController@viewPreviewMobileClaim');
        // get transaction details
        Route::post('clinic/mobile/transaction/preview', 'TransactionController@getMobileTransactionDetailsView');

        Route::get('clinic/mobile/all_transaction/preview', 'TransactionController@getMobileAllTransactionDetails');
        Route::get('clinic/mobile/transaction/details/{id}', 'TransactionController@getMobileTransactionDetails');

        // get pusher config and channel
        Route::get('pusher/config', 'HomeController@getPusherConfig');
        // get specific transaction details
        Route::get('clinic/transaction_specific', 'TransactionController@getSpecificTransactionDetails');
        // delete transaction
        Route::post('clinic/delete_transaction', 'TransactionController@deleteTransaction');
        // transaction dashboard
        Route::get('clinic/transaction_dashboard_view', 'HomeController@transactionDashboardView');
        // transaction page view
        Route::get('clinic/transaction_page_view', 'HomeController@transactionView');
        // get transaction lists by date
        Route::post('clinic/transaction_lists', 'TransactionController@searchTransaction');
        Route::post('clinic/search_transaction_lists', 'TransactionController@searchSpecificTransaction');
        // download clinic transaction lists
        Route::get('clinic/download_transaction_lists', 'TransactionController@downloadTransactions');
        // update scan pay status show
        Route::post('clinic/update_procedure_scan_pay_status', 'App_ClinicController@scanPayStatus');
        // get clinic socket connection
        Route::get('clinic_socket_connection', 'HomeController@getClinicSocketDetails');
		// api for check transaction duplication
		Route::post("check_duplicate_transaction", 'TransactionController@checkDuplicateTransaction');
		// get check in transactions
		Route::get('clinic/get_check_in_lists', 'UserCheckInController@getClinicCheckInLists');
		// get specific check in data
		Route::get('clinic/get_specific_check_in','UserCheckInController@getSpecificCheckIn');
		// remove specific check in data
		Route::post('clinic/remove_specific_check_in', 'UserCheckInController@deleteSpecificCheckIn');
		// remove specific check in data
		Route::get('clinic/auto_remove_check_in', 'UserCheckInController@checkCheckInAutoDelete');
    });

});

// admin login for all platforms
Route::get('app/login_clininic_from_admin','App_AuthController@loginClinicAdmin');
Route::get('app/login_hr_from_admin', 'BenefitsDashboardController@hrLoginAdmin');
Route::get('app/login_empoyee_from_admin', 'EclaimController@loginEmployeeAdmin');

Route::any('{path?}', function()
{
    return View::make("errors.503");
})->where("path", ".+");