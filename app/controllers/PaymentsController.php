<?php
use Illuminate\Support\Facades\Input;
class PaymentsController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	public function __construct(){

        }


	public function getPaymentHistory(){
		return View::make('settings.payments.transaction-history');
	}

	public function getPaymentInvoice(){
		$getSessionData = StringHelper::getMainSession(3);
		// $check_bank_details = DB::table('payment_partner_details')->where('partner_id', $getSessionData->Ref_ID)->count();
  //   if($check_bank_details == 0) {
  //   	$hostName = $_SERVER['HTTP_HOST'];
  //     $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  //     $data['server'] = $protocol.$hostName;
  //     $data['date'] = new DateTime();
  //     $findClinicDetails = Clinic_Library::FindClinicDetails($getSessionData->Ref_ID);
  //     $clinicArray = Array_Helper::GetClinicDetailArray($getSessionData,$findClinicDetails);
  //     $data['clinicdetails'] = $clinicArray;
  //     $data['invoice_required'] = TRUE;

  //     return View::make('settings.profile.clinic-payment-details',$data);
  //   } else {
			$findClinicDetails = Clinic_Library::FindClinicDetails($getSessionData->Ref_ID);
	    $clinicArray = Array_Helper::GetClinicDetailArray($getSessionData,$findClinicDetails);
	    $data['clinicdetails'] = $clinicArray;
	    	
			return View::make('settings.payments.transaction-invoice',$data);
    // }
	}

	public function getPaymentStatement(){
		$getSessionData = StringHelper::getMainSession(3);

		$findClinicDetails = Clinic_Library::FindClinicDetails($getSessionData->Ref_ID);

        $clinicArray = Array_Helper::GetClinicDetailArray($getSessionData,$findClinicDetails);
    	$data['clinicdetails'] = $clinicArray;

		return View::make('settings.payments.transaction-statement' ,$data);
	}

	public function updatePaymentRecordPaid( )
	{
		$input = Input::all();
		$clinic = new Clinic();
		$check_clinic = $clinic->ClinicDetails($input['clinic_id']);

		if(!$check_clinic) {
           return array(
                'status'  => 400,
                'message' => 'Clinic does not exist'
           );
	    }

		$payment_record = new PaymentRecord();
		$check_payment_record = $payment_record->getPaymentRecord($input['payment_record_id']);

	    if(!$check_payment_record) {
           return array(
                'status'  => 400,
                'message' => 'Payment Record does not exist'
           );
	    }

	    $date = date('Y-m-d', strtotime($input['payment_date']));
	    $data = array(
	    	'amount_paid'	=> $input['amount'],
	    	'remarks'		=> $input['remarks'],
	    	'payment_date'	=> $date,
	    	'status'		=> 1
	    );

	    $result = $payment_record->updatePaymentRecord($input['payment_record_id'], $data); 
		return array(
			'status'	=> 200,
			'message'	=> 'Payment Record Updated.',
			'data'		=> $result
		);
	}

	public function getToken( )
	{
		$clientToken = \Braintree_ClientToken::generate();
		return $clientToken;
	}

	public function pay( )
	{
		$input = Input::all();
		// return $input;

		// if(empty($input['stripeToken'])) {
		// 	return Redirect::back()->withErrors(['msg', 'The Message']);
		// }

		// $result = \Braintree_Transaction::sale([
		// 	'amount'				=>	$input['amount'],
		// 	'paymentMethodNonce'	=>	$input['payment_method_nonce'],
		// 	// 'merchantAccountId'		=> 'mednefits',
		// 	'customer' => [
		// 		'firstName'	=> $input['fname'],
		// 		'lastName'	=> $input['lname'],
		// 		// 'id'		=> $input['user_id']
		// 	],
		// 	'options'	=> [
		// 		'submitForSettlement'	=> true,
		// 		// 'storeInVaultOnSuccess' => true,
		// 	],
		// 	// 'serviceFeeAmount' => "1.00"
		// ]);
		// $clean_result = json_encode($result);
		// return json_decode($clean_result, true)['transaction']['_attributes']['id'];
		// if(json_decode($clean_result, true)['success'] == true) {
		// 	return "success";
		// } else {
		// 	return "error";
		// }

		// $order = array(
		// 	'user_id' 	=> $input['user_id'],
		// 	'price'		=> $input['amount'],
		// 	'quantity'	=> 1
		// );

		// $order_data = new Orders();
		// $order_id = $order_data->createOrder($order);

		// $cart = array(
		// 	'UserID'	=> $input['user_id'],
		// 	'productID'	=> $input['product_id'],
		// 	'order_id'	=> $order_id->id
		// );

		// $cart_data = new Cart();
		// $cart_data->createCart($cart);

		// $transaction = array(
		// 	'order_id' 					=> $order_id->id,
		// 	'braintree_transaction_id'	=> $result->transaction->id
		// );

		// $transaction_data = new TransactionProducts();
		// return $transaction_data->createTransaction($transaction);

		$token  = $input['stripeToken'];
		$stripe = StripeHelper::config();
		// return $stripe;
		// return $input;
		\Stripe\Stripe::setApiKey($stripe['secret_key']);

		// $plan = \Stripe\Plan::create(array(
		//   "name" => "Care Plan Subscription (Mednefits)",
		//   "id" => "care_plan_subscription_".$input['corporate_buy_start_id'],
		//   "interval" => "year",
		//   "currency" => "sgd",
		//   "amount" => 500 * 100,
		//   "proration"
		// ));

		// return $plan['id'];

		$customer = \Stripe\Customer::create(array(
	      'email' => $input['emailAddress'],
	      'source'  => $token
		));

		// return $customer;

		$charge = \Stripe\Charge::create(
			array(
					'customer' => $customer->id,
					'amount' => 200.00, 
					'currency' => 'sgd',
					'description'	=> 'Care Plan Subscrition'
				)
		);
		// $logs = new PaymentLogsStripe();
		// $logs->createLog(array('message' => serialize($charge)));
		// return var_dump($charge);
		// $clean_result = json_encode($charge);
		// return json_decode($clean_result, true);
		// return $charge['id'];
		// $charge = \Stripe\Subscription::create(
		// 		array(
		// 		  "customer" 	=> $customer->id,
		// 		  "plan" 			=> $plan['id'],
		// 		)
		// );
		return $charge;
	}

	public function getTransactionInfo($id)
	{	
			\Stripe\Stripe::setApiKey("sk_test_adGsPEwZUaB3oXGQZ63uxHiw");
			$transaction = \Stripe\Charge::retrieve($id);
		return $transaction;
		$transaction->trial_end = 'now';
		// $transaction->current_period_start = strotime(date('Y-m-d'));
		// $transaction->current_period_end = strotime('2018-08-08');
		return $transaction->save();
		// return strtotime('2018-08-08');
	}

	public function logs($id)
	{
		$data_logs = new PaymentLogsStripe();
		$result = $data_logs->getLog($id);
		return unserialize($result->message);
	}

	public function sendEventsPayment()
	{
		\Stripe\Stripe::setApiKey("sk_test_adGsPEwZUaB3oXGQZ63uxHiw");
		$input = @file_get_contents("php://input");
		// $event_json = json_decode($input);
		// return $event_json;
		return Mail::send([], [], function ($message) use ($input) {
		  $message->to('allan.alzula.work@gmail.com')
		    ->subject('web-hook')
		    ->from('info@medicloud.sg')
		    ->setBody($input);
		});
	}

	public function testPayout( )
	{
		\Stripe\Stripe::setApiKey("sk_test_adGsPEwZUaB3oXGQZ63uxHiw");

		return \Stripe\Payout::create(array(
		  'amount' => 1000,
		  'currency' => "sgd",
		));
	}

}
