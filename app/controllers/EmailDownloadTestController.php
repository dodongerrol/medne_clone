<?php

use Illuminate\Support\Facades\Input;

class EmailDownloadTestController extends \BaseController {

	public function testDownloadPDF( $folder, $filename ){
		$input = Input::all();

		$input['folder'] = $folder;    
		$input['filename'] = $filename;    

		// return $input;

		$email['clinic'] = new Clinic();
		$email['payment_record'] = new PaymentRecord();
		$email['invoice_record'] = new InvoiceRecord();

		$email['clinic_type_image']= 'https://medicloud.sg/assets/images/img-portfolio-place.png';

		$email['clinic']['Name'] = 'Jhon Doe';
		$email['clinic']['billing_name'] = 'Jhon Doe';
		$email['clinic']['billing_address'] = 'Bugo, Cagayn de Oro City';
		$email['payment_record']['invoice_number'] = 'MCD30031';
		$email['invoice_record']['start_date'] = 'January 2018';
		$email['invoice_record']['end_date'] = 'December 2018';
		$email['billing_info']['company'] = 'Jhon Company';
		$email['billing_info']['first_name'] = 'Jhon';
		$email['billing_info']['last_name'] = 'Doe';
		$email['billing_info']['address'] = 'Bugo, Cagayn de Oro City';
		$email['billing_info']['postal'] = '9000';
		$email['billing_info']['phone'] = '12321321';
		$email['billing_info']['email'] = 'jhondoe@gmail.com';

		$email['clinic_details']['clinic_name'] = 'Jhon Clinic';
		$email['clinic_details']['state'] = 'Cagayn de Oro';
		$email['clinic_details']['address'] = 'Bugo, Cagayn de Oro City';
		$email['clinic_details']['country'] = 'Philippines';
		$email['clinic_details']['postal'] = '9000';
		$email['clinic_details']['phone'] = '12321321';
		$email['clinic_details']['email'] = 'jhondoe@gmail.com';



		$email['name'] = 'Jhon Doe';
		$email['start_date'] = 'January 2018';
		$email['end_date'] = 'December 2018';
		$email['plan_start'] = 'January 2018';
		$email['plan_end'] = 'December 2018';
		$email['transaction_id'] = '123';
		$email['period'] = 'January - February';
		$email['amount_due'] = '20.00';
		$email['total_transaction'] = '20';
		$email['total_transactions'] = '20';
		$email['total_credits_transactions'] = '20';
		$email['total_cash_transactions'] = '20';
		$email['total_fees'] = '20.00';
		$email['mednefits_credits'] = '20';
		$email['mednefits_wallet'] = '20';
		$email['credits'] = '20';
		$email['total_credits'] = '20';
		$email['total'] = '20.00';
		$email['bank_details'] = false;
		$email['statement_start_date'] = 'January 2018';
		$email['statement_end_date'] = 'December 2018';
		$email['transaction_date'] = 'January - December 2018';
		$email['statement'] = 'January - December 2018';
		$email['company'] = 'Abababa Company';
		$email['health_provider_name'] = 'Abababa Company';
		$email['health_provider_address'] = 'Bugo';
		$email['health_provider_city'] = 'Cagayn de Oro City';
		$email['health_provider_country'] = 'Philippines';
		$email['health_provider_phone'] = '12343543';
		$email['emailName'] = 'Jhon Doe';
		$email['contact_name'] = 'Jhon Doe';
		$email['statement_contact_name'] = 'Jhon Doe';
		$email['first_name'] = 'Jhon';
		$email['last_name'] = 'Doe';
		$email['address'] = 'Bugo, Cagayn de Oro City';
		$email['company_address'] = 'Bugo, Cagayn de Oro City';
		$email['member'] = 'Jhon Doe';
		$email['contact_contact_number'] = '12345678';
		$email['contact_email'] = 'jhondoe@gmail.com';
		$email['statement_contact_email'] = 'jhondoe@gmail.com';
		$email['postal'] = '9000';
		$email['phone'] = '12321321';
		$email['email'] = 'jhondoe@gmail.com';
		$email['statement_total_amount'] = '50.00';
		$email['total_amount'] = '50.00';
		$email['nric'] = 'SCR0111';
		$email['service'] = 'Service';
		$email['consultation'] = 'Service';
		$email['total_consultation'] = '50';
		$email['lite_plan'] = true;
		$email['lite_plan_status'] = true;
		$email['lite_plan_enabled'] = 1;
		$email['active_plan_id'] = 'MCDR0111';
		$email['statement_number'] = 'MCDR0111';
		$email['statement_contact_number'] = 'MCDR0111';
		$email['invoice_number'] = 'MCDR0111';
		$email['invoice_date'] = 'January 2018';
		$email['paid_date'] = 'January 2018';
		$email['cancellation_number'] = 'SCR0111';
		$email['cancellation_date'] = 'December 2018';
		$email['statement_date'] = 'December 2018';
		$email['statement_due'] = 'December 2018';
		$email['amount_due'] = '50.00';
		$email['amount'] = '50.00';
		$email['amount_paid'] = '50.00';
		$email['paid_amount'] = '50.00';
		$email['total_refund'] = '50.00';
		$email['payment_due'] = 'December 2018';
		$email['invoice_due'] = 'December 2018';
		$email['number_employess'] = '5';
		$email['next_billing'] = 'December 2018';
		$email['same_as_invoice'] = 'SCR0111';
		$email['notes'] = true;
		$email['price'] = '50.00';
		$email['total_medical'] = '50.00';
		$email['total_wellness'] = '50.00';
		$email['sub_total'] = '50.00';
		$email['medical_deposit_amount'] = '50.00';
		$email['wellness_deposit_amount'] = '50.00';
		$email['paid'] = true;
		$email['complimentary'] = true;
		$email['plan_type'] = 'Standalone Plan';
		$email['account_type'] = 'Insurance Plan';
		$email['customer_active_plan_id'] = '123';
		$email['duration'] = '12 months';
		$email['payment_method'] = 'Credit';
		$email['transaction_type'] = 'Credit';
		$email['medical_status'] = true;
		$email['wellness_status'] = true;
		$email['payment_remarks'] = 'asdasdsa';
		$email['pw'] = 'asdasdsa';
		$email['password'] = 'asdasdsa';
		$email['url'] = 'asdasdsa';
		


		$email['in_network'] = array(
			0 => array(
					'date_of_transaction' => 'January 2018',
					'transaction_id' => 'MCD0011',
					'clinic_type' => 'General Practitioner',
					'clinic_type_and_service' => 'Service',
					'clinic_name' => 'Jhon Clinic',
					'amount' => '50',
					'treatment' => '50',
					'consultation' => '50',
					'mednefits_credits' => '20',
					'payment_type' => 'Mednefits credits',
					'type' => 'In-network',
					'member' => 'Jhon Doe',
					'nric' => 'SCR0111',
				),
			1 => array(
					'date_of_transaction' => 'January 2018',
					'transaction_id' => 'MCD0011',
					'clinic_type' => 'General Practitioner',
					'clinic_type_and_service' => 'Service',
					'clinic_name' => 'Jhon Clinic',
					'amount' => '50',
					'treatment' => '50',
					'consultation' => '50',
					'mednefits_credits' => '20',
					'payment_type' => 'Mednefits credits',
					'type' => 'In-network',
					'member' => 'Jhon Doe',
					'nric' => 'SCR0111',
				),
		);

		$email['users'] = array(
			0 => array(
					'name' => 'Jhon Clinic',
					'nric' => 'SCR0111',
					'period_of_used' => 'January 2018',
					'period_of_unused' => 'December 2018',
					'after_amount' => '50.00',
				),
			1 => array(
					'name' => 'Jhon Clinic',
					'nric' => 'SCR0111',
					'period_of_used' => 'January 2018',
					'period_of_unused' => 'December 2018',
					'after_amount' => '50.00',
				),
		);

		$email['transaction_details'] = array(
			0 => array(
					'visit_date' => 'January 18, 2018',
					'date_of_transaction' => 'January 18, 2018',
					'service' => 'Service',
					'clinic_type_and_service' => 'Service',
					'merchant' => 'Jhon Clinic',
					'clinic_name' => 'Jhon Clinic',
					'amount' => '50.00',
					'member' => 'Jhon Doe',
				),
		);

		$email['transactions'] = array(
			0 => array(
					'date_of_transaction' => 'January 18, 2018',
					'transaction_id' => 'MCD0011',
					'user_name' => 'Jhon Doe',
					'NRIC' => 'SCR0111',
					'procedure_name' => 'Service',
					'mednefits_fee' => '20.00',
					'mednefits_credits' => '20.00',
					'cash' => '20.00',
					'deleted' => true,
					'transaction_status' => 'Paid',
				),
		);

		// $email['emailName'] = 'Jeamar Libres';
		// $email['emailTo']   = 'jeamar1234@gmail.com';
		// $email['emailPage'] = $folder . '/' . $filename;
  // 	$email['emailSubject'] = "WELCOME TO MEDNEFITS CARE";

		// EmailHelper::sendEmail($email);
		// return $email;

		return View::make($folder . '/' . $filename, $email);

		$pdf = PDF::loadView($folder . '/' . $filename, $email);

		// return $pdf->download('SampleDownload - '.time().'.pdf');
		// return $pdf->render();

		$pdf->getDomPDF()->get_option('enable_html5_parser');
    $pdf->setPaper('A4', 'portrait');
    // $pdf->setPaper('A4', 'landscape');

		return $pdf->stream();
	}

}

?>
