// Route::get('customer/format_plan', 'BenefitsDashboardController@updatePlanAccountType');
// Route::get('test/refund/{id}', 'TransactionController@testRefund');
// Route::get('test/company/{id}', 'TransactionController@testCompany');
// Route::get('update_employee_credits', 'BenefitsDashboardController@updateEmployeeCredits');

// create manual credits claim
// Route::post('create_manual_credits_claim', 'BenefitsDashboardController@createManualCreditClaim');


// test send clinic peak hour email
// Route::get('test_clinic_peak_email', 'HomeController@testEmailClinicPeak');
// test
// Route::get('customer_active_plan_id/{customer_id}', 'BenefitsDashboardController@testGetActivePlanID');

// test routes
// Route::get('test_e_card', 'testcontroller@eCardtest');
// Route::get('test_download_plan_invoice', 'testcontroller@downloadPlanInvoice');
// Route::get('test_claim_submission', 'testcontroller@testSubmitClaimData');
// Route::get('test_socket_connection', 'HomeController@testSocketConnection');
// Route::get('test/new_pdf', 'InvoiceController@testNewPDF');
// Route::get('show/new_pdf', 'InvoiceController@showNewPDF');


// Route::get('email/test_download/{folder}/{filename}', 'EmailDownloadTestController@testDownloadPDF');


// check out-of-network in existing in-network
// Route::get('check_out_of_network/{id}', 'EclaimController@checkOutofNetwork');

// Route::get('insert_deleted_transaction', 'TransactionController@insertDeletedToTransaction');
// Route::post('app/clinic/get_transaction_previews', 'TransactionController@getTransactionView');
// format invoice invoice_date
// Route::get('format/invoice_details', 'BenefitsDashboardController@formatInvoiceDate');
// Route::get('format/invoice', 'BenefitsDashboardController@newFormatInvoiceNumber');
// test calculate
// Route::post('test/calculate', 'TransactionController@testCalculate');
// update dental discounts
// Route::get('update/clinic_dental_discount', 'TransactionController@updateClinicDiscount');
// Route::get('update/clinic_dental_transaction_discount', 'TransactionController@updateClinicDentalTransactions');
// Route::get('test/email_send', 'HomeController@testEmailSend');
// insert local network
// Route::post('insert/local_network', 'NetworkPatnerController@createLocalNetwork');
// Route::post('insert/local_network_partners', 'NetworkPatnerController@createLocalNetworkPartners');
// save webhook data from stripe
// Route::post('save/webhook/stripe', 'WebHookController@saveWebHookStripeLogs');

// test debug json
// Route::post('test/debug/json', 'HomeController@testDebugJson');
// payment test
// Route::get('payment/token', 'PaymentsController@getToken');
// Route::get('payment/get_transaction/{id}', 'PaymentsController@getTransactionInfo');
// Route::post('payment/pay', 'CarePlanPurchaseController@payCreditCardPlan');
// Route::post('payment/pay', 'PaymentsController@pay');
// Route::get('get/logs/{id}', 'PaymentsController@logs');
// Route::post('send/events/payment', 'PaymentsController@sendEventsPayment');
// Route::get('payment/view', function( ){
//     return View::make('payment');
// });
// Route::get('test_call/{number}', 'SMSController@call');
// Route::post('accept_call', 'SMSController@getCall');
// Route::get('fixed_gp', 'TransactionController@fixedGP');
// Route::get('app/reset/', 'App_ClinicController@resetValue');
