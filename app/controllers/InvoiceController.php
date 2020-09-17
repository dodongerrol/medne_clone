<?php
use Illuminate\Support\Facades\Input;
class InvoiceController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function invoiceListsByDate( )
	{
		$input = Input::all();
		$clinic = new Clinic();
		$invoice_records = [];
		$all_invoice = [];
		$check_clinic = $clinic->ClinicDetails($input['clinic_id']);

		if(!$check_clinic) {
			return array(
				'status'  => 400,
				'message' => 'Clinic does not exist'
			);
		}

		$start = date('Y-m-d', strtotime($input['start']));
			// $end = date('Y-m-d', strtotime($input['end']));

		$invoice_data = [];
		$invoice_record_detail = new InvoiceRecordDetails();
		$payment_record = new PaymentRecord();
		$invoice = new InvoiceRecord();
		$clinic = new Clinic();
		$transaction = new Transaction();
		$statement = new StatementOfAccount();
		$get_clinic_ids = $invoice->checkStatementInvoice($start, $input['clinic_id']);

	    // return $get_clinic_id;

		foreach ($get_clinic_ids as $key => $get_clinic_id) {
			$all_payment_records = $payment_record->getPaymentRecordByInvoiceListClinic($get_clinic_id['invoice_id']);
		    // return $all_payment_records;
			foreach ($all_payment_records as $key => $value) {
				$check_statement = $statement->checkGenerateStatus($value->payment_record_id);
				if($check_statement == 1) {
					$total = 0;

					$invoice_data['payment'] = $payment_record->getPaymentRecordClinic($value->invoice_id);
					$invoice_data['clinic'] = $clinic->ClinicDetails($value->clinic_id);
					$invoice_data['invoice'] = $invoice->getInvoiceClinic($value->invoice_id);

					$transactions = [];
					$details = [];
					$total = 0;
					$mednefits_total_fee = 0;
					$mednefits_total_credits = 0;
					$total_cash = 0;
					$total_procedure = 0;
					$total_percentage = 0;
					$total_transaction = 0;
					$total_fees = 0;
					$total_credits_transactions = 0;
					$total_cash_transactions = 0;
					$amount_due = null;
					$transaction_results = $invoice_record_detail->getTransaction($value->invoice_id);

					foreach ($transaction_results as $key => $transact) {

						$trans = DB::table('transaction_history')
						->join('user', 'user.UserID', '=', 'transaction_history.UserID')
						->where('transaction_history.transaction_id', $transact->transaction_id)
						->where('transaction_history.deleted', 0)
						->where('transaction_history.paid', 1)
						->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.gst_percent_value')
						->first();

						if($trans) {
							if($trans->deleted == 0 || $trans->deleted == "0") {
								$procedure_temp = "";
								$procedure = "";
								$procedure_ids = [];
								$mednefits_total_fee += $trans->credit_cost;
								if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
									$mednefits_total_credits += $trans->credit_cost;
								}

								if($trans->co_paid_status == 0) {
									if(strrpos($trans->clinic_discount, '%')) {
										$percentage = chop($trans->clinic_discount, '%');
										if($trans->credit_cost > 0) {
											$amount = $trans->credit_cost;
										} else {
											$amount = $trans->procedure_cost;
										}

										$total_percentage = $percentage + $trans->medi_percent;

										$formatted_percentage = $total_percentage / 100;
										$temp_fee = $amount / ( 1 - $formatted_percentage );
											// if non gst
										$mednefits_pecent = $trans->medi_percent / 100;
										$fee = $temp_fee * $mednefits_pecent;
									} else {
										$fee = number_format((float)$trans->co_paid_amount, 2);
									}
								} else {
									if(strrpos($trans->clinic_discount, '%')) {
										$percentage = chop($trans->clinic_discount, '%');
										if($trans->credit_cost > 0) {
											$amount = $trans->credit_cost;
										} else {
											$amount = $trans->procedure_cost;
										}

										$total_percentage = $percentage + $trans->medi_percent;

										$formatted_percentage = $total_percentage / 100;
										$temp_fee = $amount / ( 1 - $formatted_percentage );
											// if non gst
										$mednefits_pecent = $trans->medi_percent / 100;
										$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
										$fee = $temp_mednefits_fee * $trans->gst_percent_value;
									} else {
										$fee = number_format((float)$trans->co_paid_amount, 2);
									}
								}
								$total_fees += $fee;

								if($trans->credit_cost > 0) {
									$mednefits_credits = number_format($trans->credit_cost, 2);
									$cash = 0.00;
									$total_credits_transactions++;
								} else {
									$mednefits_credits = 00;
									$cash = number_format($trans->procedure_cost);
									$total_cash_transactions++;
								}

								if($trans->health_provider_done == 1 && $trans->credit_cost == 0 || $trans->health_provider_done == "1" && $trans->credit_cost == "0") {
									$total_cash += $trans->procedure_cost;
								}

								$mednefits_total_fee += $fee;
								$total_transaction++;
							}

						}

					}

					$paid_amount = (float)$value->amount_paid;
			    // $transactions['transaction_lists'] = $transaction_data;
					$invoice_record = array(
						'clinic_id'		=> $invoice_data['invoice']['clinic_id'],
						'invoice_id'	=> $invoice_data['invoice']['invoice_id'],
						'start_date'	=> date('Y-m-01', strtotime($invoice_data['invoice']['start_date'])),
						'end_date'		=> date('Y-m-t', strtotime($invoice_data['invoice']['start_date'])),
						'created_at'	=> $invoice_data['invoice']['created_at'],
						'updated_at'	=> $invoice_data['invoice']['updated_at']
					);
					$transactions['period'] = date('j M', strtotime($invoice_data['invoice']['start_date'])).' - '.date('j M Y', strtotime($invoice_data['invoice']['end_date']));
					$transactions['invoice_record'] = $invoice_record;
					$transactions['invoice_due'] = $invoice_record['end_date'];
					$transactions['total'] = number_format($mednefits_total_fee, 2);
					$transactions['mednefits_fee'] = number_format($mednefits_total_fee, 2);
					$transactions['mednefits_credits'] = number_format($mednefits_total_credits, 2);
					$transactions['total_cash'] = number_format($total_cash, 2);
					$transactions['clinic'] = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
			    // $transactions['total_transaction'] = sizeof($transaction_data);
					$transactions['total_transaction'] = $total_transaction;
					$transactions['total_fees'] = number_format($total_fees, 2);
					$transactions['total_credits_transactions'] = $total_credits_transactions;
					$transactions['total_cash_transactions'] = $total_cash_transactions;
					$transactions['payment'] = $value;
					$balance = $mednefits_total_fee - $paid_amount;

					if($balance > 0) {
						$amount_due = number_format($balance, 2);
					} else {
						$amount_due = number_format(0, 2);
					}
					$transactions['amount_due'] = $amount_due;

					$all_invoice[] = $transactions;
				}
			}

		}


		$data['result'] = $all_invoice;
	    // return $data;
		return View::make('settings.payments.transaction-statement-table-view', $data);
	}

	public function invoiceLists( )
	{
		$input = Input::all();
		$clinic = new Clinic();
		$invoice_records = [];
		$check_clinic = $clinic->ClinicDetails($input['clinic_id']);

		if(!$check_clinic) {
			return array(
				'status'  => 400,
				'message' => 'Clinic does not exist'
			);
		}

		$invoice_data = [];
		$all_invoice = [];
		$invoice_record_detail = new InvoiceRecordDetails();
		$payment_record = new PaymentRecord();
		$invoice = new InvoiceRecord();
		$clinic = new Clinic();
		$transaction = new Transaction();

		$all_payment_records = $payment_record->getPaymentRecordListClinict($input['clinic_id']);

		foreach ($all_payment_records as $key => $value) {
			$total = 0;

			$invoice_data['payment'] = $payment_record->getPaymentRecordClinic($value->invoice_id);
			$invoice_data['clinic'] = $clinic->ClinicDetails($value->clinic_id);

			$transaction_results = $invoice_record_detail->getTransaction($value->invoice_id);
	    // return $transaction_results;
			foreach ($transaction_results as $key => $value_2) {
				$trans = $transaction->getTransactionById($value_2->transaction_id);
	    	// return $trans;
	    	// if((int)$trans['procedure_cost'] <= 500) {
	    	// 	if((int)$trans['credit_cost'] > 0) {
	    	// 		$clinic_cost = $trans['procedure_cost'] * $trans['medi_percent'];
	    	// 		$amount = (int)$trans['credit_cost'] + $clinic_cost;
	    	// 	} else {
	    	// 		$amount = $trans['procedure_cost'] * $trans['medi_percent'];
	    	// 	}
	    	// 	$total += $amount;
	    	// } else {
	    	// 	$amount = 0;
	    	// }
				if($trans['transaction']['procedure_cost'] <= 500) {
    		// if($trans['transaction']['credit_cost'] > 0) {
					if($trans['transaction']['co_paid_status'] == 1) {
    				// $discount_clinic = str_replace('$', '', $trans['transaction']['clinic_discount']);
    				// $clinic_cost = $discount_clinic + $trans['transaction']['co_paid_amount'];
    				// $procedure_total = $trans['transaction']['procedure_cost'] - $clinic_cost;
    				// $amount = $trans['transaction']['credit_cost'] + $procedure_total;
						$amount = $trans['transaction']['credit_cost'] + $trans['transaction']['co_paid_amount'];
						$trans['discount_value'] = '$'.$trans['transaction']['co_paid_amount'];
					} else {
						$clinic_cost = $trans['transaction']['procedure_cost'] * $trans['transaction']['medi_percent'];
						$amount = $trans['transaction']['credit_cost'] + $clinic_cost;
						$trans['discount_value'] = $trans['transaction']['medi_percent'] * 100 .'%';
					}
	    		// } else {
	    		// 	$amount = $trans['transaction']['procedure_cost'] * $trans['transaction']['medi_percent'];
	    		// }
					$total += $amount;
	    		// $amount = $trans['transaction']['procedure_cost'] * $trans['transaction']['medi_percent'];
	    		// $total += $amount;
					$total_percentage += $trans['transaction']['medi_percent'];
					$total_procedure += $trans['transaction']['procedure_cost'];
				} else {
					$amount = 0;
				}
				$amount = 0;
			}

			if((int)$invoice_data['payment']['amount_paid'] != 0 || $invoice_data['payment']['amount_paid'] != null || $invoice_data['payment']['status'] == 1) {
				$due = 0;
			} else {
				$due = $total;
			}

			$invoice_data['total'] = $total;
			$invoice_data['amount_due'] = $due;

			$all_invoice[] = $invoice_data;

		}

    // return $all_invoice;

		$data['result'] = $all_invoice;

		return View::make('settings.payments.transaction-statement-table-view', $data);

    // $total = 0;

    // $check_invoice = $invoice->checkInvoice($input);
    // $invoice_data['payment'] = $payment_record->getPaymentRecordClinic($check_invoice['invoice_id']);
    // $invoice_data['clinic'] = $clinic->ClinicDetails($input['clinic_id']);

    // $transaction_results = $invoice_record_detail->getTransaction($check_invoice['invoice_id']);
    // foreach ($transaction_results as $key => $value) {
    // 	$trans = $transaction->getTransactionById($value->transaction_id);
    // 	if((int)$trans['procedure_cost'] <= 500) {
    // 		$amount = $trans['procedure_cost'] * $trans['medi_percent'];
    // 		$total += $amount;
    // 	} else {
    // 		$amount = 0;
    // 	}
    // 	$amount = 0;
    // }

    // if((int)$invoice_data['payment']['amount_paid'] != 0 || $invoice_data['payment']['amount_paid'] != null || $invoice_data['payment']['status'] == 1) {
    // 	$due = 0;
    // } else {
    // 	$due = $total;
    // }

    // $invoice_data['total'] = $total;
    // $invoice_date['amount_due'] = $due;
    // return $invoice_data;
	}

	public function downloadInvoice($id)
	{
		$invoice_record_detail = new InvoiceRecordDetails();
		$invoice_data = DB::table('invoice_record')->where('invoice_id', $id)->first();

		if(!$invoice_data) {
			return array(
				'status'	=> 400,
				'message'	=> "No transaction found in the chosen month."
			);
		}

		$transaction = new Transaction();
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$doctor = new Doctor();
		$procedure = new ClinicProcedures();
		$user = new User();
		$bank = new PartnerDetails();

		$transactions = [];
		$transaction_data = [];
		$details = [];
		$total = 0;
		$mednefits_total_fee = 0;
		$mednefits_total_credits = 0;
		$total_cash = 0;
		$total_procedure = 0;
		$total_percentage = 0;
		$total_transaction = 0;
		$total_fees = 0;
		$total_credits_transactions = 0;
		$total_cash_transactions = 0;

		$transaction_results = $invoice_record_detail->getTransaction($id);

		foreach ($transaction_results as $key => $value) {
			$trans = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.transaction_id', $value->transaction_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.paid', 1)
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.gst_percent_value', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount')
			->first();

			if($trans) {
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$procedure_temp = "";
					$procedure = "";
					$procedure_ids = [];
					$mednefits_total_fee += $trans->credit_cost;
					if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
						$mednefits_total_credits += $trans->credit_cost;
					}
					if($trans->co_paid_status == 0) {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
							// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$fee = $temp_fee * $mednefits_pecent;
						} else {
							if((int)$trans->peak_hour_status == 1) {
								$fee = number_format((float)$trans->peak_hour_amount, 2);
							} else {
								$fee = number_format((float)$trans->co_paid_amount, 2);
							}
						}
					} else {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
							// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
							$fee = $temp_mednefits_fee * $trans->gst_percent_value;
						} else {
							if((int)$trans->peak_hour_status == 1) {
								$fee = number_format((float)$trans->peak_hour_amount, 2);
							} else {
								$fee = number_format((float)$trans->co_paid_amount, 2);
							}
						}
					}
					$total_fees += $fee;

					if($trans->credit_cost > 0) {
						$mednefits_credits = $trans->credit_cost;
						$cash = 0.00;
						$total_credits_transactions++;
					} else {
						$mednefits_credits = 00;
						$cash = number_format($trans->procedure_cost);
						$total_cash_transactions++;
					}

					if($trans->health_provider_done == 1 && $trans->credit_cost == 0 || $trans->health_provider_done == "1" && $trans->credit_cost == "0") {
						$total_cash += $trans->procedure_cost;
					}

					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();

					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$temp = array(
						'transaction_id'    		=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'ClinicID'							=> $trans->ClinicID,
						'NRIC'									=> $trans->NRIC,
					// 'ProcedureID'						=> $procedure_id,
						'UserID'								=> $trans->UserID,
						'date_of_transaction'		=> date('d F Y', strtotime($trans->date_of_transaction)),
						'paid'									=> $trans->paid,
						'procedure_cost'				=> $trans->procedure_cost,
						'services'							=> $procedure,
						'customer'							=> ucwords($trans->user_name),
						'mednefits_fee'					=> number_format($fee, 2),
						'discount'							=> $trans->clinic_discount,
						'multiple_procedures' 	=> $trans->multiple_service_selection,
						'health_provider'				=> $trans->health_provider_done,
						'mednefits_credits'			=> number_format($mednefits_credits, 2),
						'cash'									=> number_format($cash, 2),
						'procedure_ids'					=> $procedure_ids,
						'total'									=> number_format($fee + $mednefits_credits, 2),
						'currency_type'					=> "SGD"
					);
					array_push($transaction_data, $temp);

					$mednefits_total_fee += $fee;
					$total_transaction++;
				}
			}
		}

		if($total_transaction == 0) {
			return array(
				'status'	=> 400,
				'message'	=> "No transaction found in the chosen month."
			);
		}

		$get_payment_record = \PaymentRecord::where('invoice_id', $id)->first();
		$get_payment_details = $bank->getBankDetails($get_payment_record->clinic_id);
		$transactions['payment_record'] = $get_payment_record;
		$transactions['bank_details'] = $get_payment_details;
		$paid_amount = (float)$transactions['payment_record']['amount_paid'];

		$end_date = date('Y-m-t', strtotime('+1 month', strtotime($invoice_data->start_date)));
		$invoice_record = array(
			'clinic_id'		=> $invoice_data->clinic_id,
			'invoice_id'	=> $invoice_data->invoice_id,
			'start_date'	=> date('F d, Y', strtotime('+1 month', strtotime($invoice_data->start_date))),
			'end_date'		=> date('F d, Y', strtotime($end_date)),
			'created_at'	=> $invoice_data->created_at,
			'updated_at'	=> $invoice_data->updated_at
		);
		$transactions['period'] = date('j M', strtotime($invoice_data->start_date)).' - '.date('j M Y', strtotime($invoice_data->end_date));
		$transactions['invoice_record'] = $invoice_record;
		$transactions['invoice_due'] = $invoice_record['end_date'];
		$transactions['total'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_fee'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_credits'] = number_format($mednefits_total_credits, 2);
		$transactions['total_cash'] = number_format($total_cash, 2);
		$transactions['clinic'] = DB::table('clinic')->where('ClinicID', $invoice_data->clinic_id)->first();
    // $transactions['total_transaction'] = sizeof($transaction_data);
		$transactions['total_transaction'] = $total_transaction;
		$transactions['total_fees'] = number_format($total_fees, 2);
		$transactions['total_credits_transactions'] = $total_credits_transactions;
		$transactions['total_cash_transactions'] = $total_cash_transactions;
		$transactions['currency_type'] = "SGD";
		$transactions['transactions'] = $transaction_data;
		$balance = $mednefits_total_fee - $paid_amount;

		if($balance > 0) {
			$amount_due = number_format($balance, 2);
		} else if($balance < 0) {
			$amount_due = number_format(0, 2);
		} else {
			$amount_due = number_format($balance, 2);
		}

		$transactions['amount_due'] = number_format($balance, 2);
    // return View::make('pdf-download.clinic_invoice', $transactions);
		$pdf = PDF::loadView('pdf-download.clinic_invoice', $transactions);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');


		return $pdf->download($get_payment_record->invoice_number.' - '.time().'.pdf');
    // return $transactions;
	}

	public function createInvoice()
	{
		$input = Input::all();
		$clinic = new Clinic();
		$invoice_records = [];
		$check_clinic = $clinic->ClinicDetails($input['clinic_id']);

		if(!$check_clinic) {
			return array(
				'status'  => 400,
				'message' => 'Clinic does not exist'
			);
		}

	  // $check_bank_details = DB::table('payment_partner_details')->where('partner_id', $input['clinic_id'])->count();
   //  return $check_bank_details;

		$transaction = new Transaction();
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$invoice_record_detail = new InvoiceRecordDetails();
		$doctor = new Doctor();
		$procedure = new ClinicProcedures();
		$user = new User();
		$bank = new PartnerDetails();

		$check_transaction = $transaction->checkTransaction($input['clinic_id'], $input);
	    // return $check_transaction;
		if($check_transaction == 0) {
			return array(
				'status'	=> 400,
				'message'	=> "No transaction found in the chosen month."
			);
		}

		$check_invoice = $invoice->checkInvoice($input);
	    // return $check_invoice;
		if($check_invoice) {
	    	// return "hello";
			$invoice_id = $check_invoice['invoice_id'];
			$invoice_data =  $check_invoice;
			$transaction_list = $transaction->getDateTransaction($check_invoice);
	    	// return $transaction_list;
			$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $check_invoice['invoice_id'], $check_invoice['clinic_id']);
			$check_payment_record = $payment_record->insertOrGet($check_invoice['invoice_id'], $input['clinic_id']);
		} else {
	    	// return "hi";
			$result_create = $invoice->createInvoice($input);
			$invoice_data = $result_create;
	    	// return $result_create->id;
			$invoice_id = $result_create->id;
			$transaction_list = $transaction->getDateTransaction($result_create);
	    	// return $transaction_list;
			$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $result_create->id, $input['clinic_id']);
			$check_payment_record = $payment_record->insertOrGet($result_create->id, $input['clinic_id']);
	    	// $check_payment_record['payment_record_id'] = $check_payment_record->id;
		}

	    // return $invoice_id;
		$transactions = [];
		$details = [];
		$total = 0;
		$mednefits_total_fee = 0;
		$mednefits_total_credits = 0;
		$total_cash = 0;
		$total_procedure = 0;
		$total_percentage = 0;
		$total_transaction = 0;
		$total_fees = 0;
		$total_credits_transactions = 0;
		$total_cash_transactions = 0;
		$amount_due = null;
		$transaction_results = $invoice_record_detail->getTransaction($invoice_id);
		$transaction_data = [];
	    // return $transaction_results;
		foreach ($transaction_results as $key => $value) {

	    		// $trans = DB::table('transaction_history')->where('transaction_id', $value->transaction_id)->where('deleted', 0)->first();
			$trans = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.transaction_id', $value->transaction_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.paid', 1)
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.gst_percent_value', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount')
			->first();

			if($trans) {
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$procedure_temp = "";
					$procedure = "";
					$procedure_ids = [];
					$mednefits_total_fee += $trans->credit_cost;
					if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
						$mednefits_total_credits += $trans->credit_cost;
					}

					if($trans->co_paid_status == 0) {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
									// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$fee = $temp_fee * $mednefits_pecent;
						} else {
							if((int)$trans->peak_hour_status == 1) {
								$fee = number_format((float)$trans->peak_hour_amount, 2);
							} else {
								$fee = number_format((float)$trans->co_paid_amount, 2);
							}
						}
					} else {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
									// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
							$fee = $temp_mednefits_fee * $trans->gst_percent_value;
						} else {
							if((int)$trans->peak_hour_status == 1) {
								$fee = number_format((float)$trans->peak_hour_amount, 2);
							} else {
								$fee = number_format((float)$trans->co_paid_amount, 2);
							}
						}
					}
					$total_fees += $fee;

					if($trans->credit_cost > 0) {
						$mednefits_credits = $trans->credit_cost;
						$cash = 0.00;
						$total_credits_transactions++;
					} else {
						$mednefits_credits = 0;
						$cash = number_format($trans->procedure_cost);
						$total_cash_transactions++;
					}

					if($trans->health_provider_done == 1 && $trans->credit_cost == 0 || $trans->health_provider_done == "1" && $trans->credit_cost == "0") {
						$total_cash += $trans->procedure_cost;
					}

					$mednefits_total_fee += $fee;
					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$temp = array(
						'ClinicID'							=> $trans->ClinicID,
						'NRIC'									=> $trans->NRIC,
						'UserID'								=> $trans->UserID,
						'date_of_transaction'		=> date('d F Y', strtotime($trans->date_of_transaction)),
						'paid'									=> $trans->paid,
						'procedure_cost'				=> $trans->procedure_cost,
						'services'							=> $procedure,
						'transaction_id'    		=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'customer'							=> ucwords($trans->user_name),
						'mednefits_fee'					=> number_format($fee, 2),
						'discount'							=> $trans->clinic_discount,
						'multiple_procedures' 	=> $trans->multiple_service_selection,
						'health_provider'				=> $trans->health_provider_done,
						'mednefits_credits'			=> number_format($mednefits_credits, 2),
						'cash'									=> number_format($cash, 2),
						'procedure_ids'					=> $procedure_ids,
						'total'									=> number_format($fee + $mednefits_credits, 2),
						'currency_type'					=> "SGD"
					);
					array_push($transaction_data, $temp);
					$total_transaction++;
				}

			}

		}

		if($total_transaction === 0) {
			return array(
				'status'	=> 400,
				'message'	=> "No transaction found in the chosen month."
			);
		}

		if($mednefits_total_fee > 0) {
			DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "true"]);
		} else {
			DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "false"]);
		}

		$get_payment_record = $payment_record->getPaymentRecord($check_payment_record);
		$get_payment_details = $bank->getBankDetails($input['clinic_id']);
		$transactions['payment_record'] = $get_payment_record;
		$transactions['bank_details'] = $get_payment_details;
		$paid_amount = (float)$transactions['payment_record']['amount_paid'];
		$transactions['transaction_lists'] = $transaction_data;
		$invoice_record = array(
			'clinic_id'		=> $invoice_data['clinic_id'],
			'invoice_id'	=> $invoice_data['invoice_id'],
			'start_date'	=> date('Y-m-01', strtotime('+1 month', strtotime($invoice_data['start_date']))),
			'end_date'		=> date('Y-m-t', strtotime('+1 month', strtotime($invoice_data['start_date']))),
			'created_at'	=> $invoice_data['created_at'],
			'updated_at'	=> $invoice_data['updated_at']
		);
		$transactions['period'] = date('j M', strtotime($invoice_data['start_date'])).' - '.date('j M Y', strtotime($invoice_data['end_date']));
		$transactions['invoice_record'] = $invoice_record;
		$transactions['invoice_due'] = $invoice_record['end_date'];
		$transactions['total'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_fee'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_credits'] = number_format($mednefits_total_credits, 2);
		$transactions['total_cash'] = number_format($total_cash, 2);
		$transactions['clinic'] = $check_clinic;
		$transactions['total_transaction'] = $total_transaction;
		$transactions['total_fees'] = number_format($total_fees, 2);
		$transactions['total_credits_transactions'] = $total_credits_transactions;
		$transactions['total_cash_transactions'] = $total_cash_transactions;
		$transactions['billing_name'] = $check_clinic->billing_name ? ucwords($check_clinic->billing_name) : $check_clinic->Name;
		$transactions['billing_address'] = $check_clinic->billing_address ? ucwords($check_clinic->billing_address) : $check_clinic->Address;
		$balance = $mednefits_total_fee - $paid_amount;
	    // if($balance > 0) {
		$amount_due = number_format($balance, 2);
	    // } else {
	    // 	$amount_due = number_format(0, 2);
	    // }
		$transactions['amount_due'] = $amount_due;
		$transactions['currency_type'] = "SGD";

		return $transactions;
	}


	public function getAdminInvoice( )
	{
		$all_invoice = [];
		$invoice_data = [];
		$invoice_record_detail = new InvoiceRecordDetails();
		$payment_record = new PaymentRecord();
		$invoice = new InvoiceRecord();
		$clinic = new Clinic();
		$transaction = new Transaction();

		$all_payment_records = $payment_record->getPaymentRecordList();

	    // return $all_payment_records;
		foreach ($all_payment_records as $key => $value) {
			$total = 0;

			$invoice_data['payment'] = $payment_record->getPaymentRecordClinic($value->invoice_id);
			$invoice_data['clinic'] = $clinic->ClinicDetails($value->clinic_id);

			$transaction_results = $invoice_record_detail->getTransaction($value->invoice_id);
		    // return $transaction_results;
			foreach ($transaction_results as $key => $value_2) {
				$trans = $transaction->getTransactionById($value_2->transaction_id);
				if((int)$trans['procedure_cost'] <= 500) {
					$amount = $trans['procedure_cost'] * $trans['medi_percent'];
					$total += $amount;
				} else {
					$amount = 0;
				}
				$amount = 0;
			}

			if((int)$invoice_data['payment']['amount_paid'] != 0 || $invoice_data['payment']['amount_paid'] != null || $invoice_data['payment']['status'] == 1) {
				$due = 0;
			} else {
				$due = $total;
			}


			$invoice_data['total'] = $total;
			$invoice_data['amount_due'] = $due;
			$invoice_data['due_date']	= $invoice->getInvoiceClinic($value->invoice_id);
			$all_invoice[] = $invoice_data;

		}

		return $all_invoice;
	}

	public function getClinicStatement($id)
	{
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$statement = new StatementOfAccount();


		// if($statement_data['payment_record']['status'] == 0) {
		// 	return array(
		// 		'status'	=> 400,
		// 		'message'	=> 'No transaction found in the chosen month.'
		// 	);
		// }


		$transactions = [];
		$invoice_record_detail = new InvoiceRecordDetails();
		$clinic = new Clinic();
		$transaction = new Transaction();
		$bank = new PartnerDetails();

		$total = 0;

		$transactions['statement'] = $statement->getClinicStatement($id);
		$transactions['payment_record'] = $payment_record->getPaymentRecord($transactions['statement']['payment_record_id']);
		$invoice_record_data = \InvoiceRecord::where('invoice_id', $transactions['payment_record']['invoice_id'])->first();
		// return $invoice_record_data;
		$transactions['clinic'] = $clinic->ClinicDetails($transactions['payment_record']['clinic_id']);
		$check_payment_record = $payment_record->insertOrGet($transactions['payment_record']['invoice_id'], $transactions['payment_record']['clinic_id']);

		$details = [];
		$total = 0;
		$mednefits_total_fee = 0;
		$mednefits_total_credits = 0;
		$total_cash = 0;
		$total_procedure = 0;
		$total_percentage = 0;
		$total_transaction = 0;
		$total_fees = 0;
		$total_credits_transactions = 0;
		$total_cash_transactions = 0;
		$amount_due = null;
		$transaction_results = $invoice_record_detail->getTransaction($transactions['payment_record']['invoice_id']);
    // $transaction_data = [];
    // return $transaction_results;
		foreach ($transaction_results as $key => $value) {
			$trans = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.transaction_id', $value->transaction_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.paid', 1)
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.gst_percent_value')
			->first();

			if($trans) {
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$procedure_temp = "";
					$procedure = "";
					$procedure_ids = [];
					$mednefits_total_fee += $trans->credit_cost;
					if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
						$mednefits_total_credits += $trans->credit_cost;
					}

					if($trans->co_paid_status == 0) {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
								// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$fee = $temp_fee * $mednefits_pecent;
						} else {
							$fee = number_format((float)$trans->co_paid_amount, 2);
						}
					} else {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
								// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
							$fee = $temp_mednefits_fee * $trans->gst_percent_value;
						} else {
							$fee = number_format((float)$trans->co_paid_amount, 2);
						}
					}
					$total_fees += $fee;

					if($trans->credit_cost > 0) {
						$mednefits_credits = number_format($trans->credit_cost, 2);
						$cash = 0.00;
						$total_credits_transactions++;
					} else {
						$mednefits_credits = 00;
						$cash = number_format($trans->procedure_cost);
						$total_cash_transactions++;
					}

					if($trans->health_provider_done == 1 && $trans->credit_cost == 0 || $trans->health_provider_done == "1" && $trans->credit_cost == "0") {
						$total_cash += $trans->procedure_cost;
					}

					$mednefits_total_fee += $fee;

						// $temp = array(
						// 	'ClinicID'							=> $trans->ClinicID,
						// 	'NRIC'									=> $trans->NRIC,
						// 	'ProcedureID'						=> $procedure_id,
						// 	'UserID'								=> $trans->UserID,
						// 	'date_of_transaction'		=> date('d F Y', strtotime($trans->date_of_transaction)),
						// 	'paid'									=> $trans->paid,
						// 	'procedure_cost'				=> $trans->procedure_cost,
						// 	'services'							=> $procedure,
						// 	'transaction_id'				=> $trans->transaction_id,
						// 	'customer'							=> ucwords($trans->user_name),
						// 	'mednefits_fee'					=> number_format($fee, 2),
						// 	'discount'							=> $trans->clinic_discount,
						// 	'multiple_procedures' 	=> $trans->multiple_service_selection,
						// 	'health_provider'				=> $trans->health_provider_done,
						// 	'mednefits_credits'			=> number_format($mednefits_credits, 2),
						// 	'cash'									=> number_format($cash, 2),
						// 	'procedure_ids'					=> $procedure_ids,
						// 	'total'									=> $fee + $mednefits_credits
						// );
			   //  	array_push($transaction_data, $temp);
					$total_transaction++;
				}

			}

		}

		if($mednefits_total_fee > 0) {
			DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "true"]);
		} else {
			DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "false"]);
		}

		$get_payment_record = $payment_record->getPaymentRecord($check_payment_record);
		$get_payment_details = $bank->getBankDetails($transactions['payment_record']['clinic_id']);
		$transactions['payment_record'] = $get_payment_record;
		$transactions['bank_details'] = $get_payment_details;
		$paid_amount = (float)$transactions['payment_record']['amount_paid'];
    // $transactions['transaction_lists'] = $transaction_data;
		$invoice_record = array(
			'clinic_id'		=> $invoice_record_data->clinic_id,
			'invoice_id'	=> $invoice_record_data->invoice_id,
			'start_date'	=> date('Y-m-01', strtotime('+1 month', strtotime($invoice_record_data->start_date))),
			'end_date'		=> date('Y-m-t', strtotime('+1 month', strtotime($invoice_record_data->start_date))),
			'created_at'	=> $invoice_record_data->created_at,
			'updated_at'	=> $invoice_record_data->updated_at
		);
		$transactions['period'] = date('j M', strtotime($invoice_record_data->start_date)).' - '.date('j M Y', strtotime($invoice_record_data->end_date));
		$transactions['invoice_record'] = $invoice_record;
		$transactions['invoice_due'] = $invoice_record['end_date'];
		$transactions['total'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_fee'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_credits'] = number_format($mednefits_total_credits, 2);
		$transactions['total_cash'] = number_format($total_cash, 2);
		$transactions['clinic'] = DB::table('clinic')->where('ClinicID', $transactions['payment_record']['clinic_id'])->first();
    // $transactions['total_transaction'] = sizeof($transaction_data);
		$transactions['total_transaction'] = $total_transaction;
		$transactions['total_fees'] = number_format($total_fees, 2);
		$transactions['total_credits_transactions'] = $total_credits_transactions;
		$transactions['total_cash_transactions'] = $total_cash_transactions;
		$transactions['billing_name'] = $transactions['clinic']->billing_name ? ucwords($transactions['clinic']->billing_name) : $transactions['clinic']->Name;
		$transactions['billing_address'] = $transactions['clinic']->billing_address ? ucwords($transactions['clinic']->billing_address) : $transactions['clinic']->Address;

		$balance = $mednefits_total_fee - $paid_amount;

		if($balance > 0) {
			$amount_due = number_format($balance, 2);
		} else {
			$amount_due = number_format(0, 2);
		}
		$transactions['amount_due'] = $amount_due;
		$transactions['ending_balance'] = number_format($mednefits_total_fee - $get_payment_record->amount_paid, 2);
	 //    foreach ($statement_data['transactions']  as $key => $value_2) {
	 //    	$trans = DB::table('transaction_history')->where('transaction_id', $value_2->transaction_id)
	 //    						->where('paid', 1)->where('deleted', 0)->first();
	 //    	if($trans) {
		//     	if($trans->co_paid_status == 0) {
		// 				if(strrpos($trans->clinic_discount, '%')) {
		// 					$percentage = chop($trans->clinic_discount, '%');
		// 					if($trans->credit_cost > 0) {
		// 						$amount = $trans->credit_cost;
		// 					} else {
		// 						$amount = $trans->procedure_cost;
		// 					}

		// 					$total_percentage = $percentage + $trans->medi_percent;

		// 					$formatted_percentage = $total_percentage / 100;
		// 					$temp_fee = $amount / ( 1 - $formatted_percentage );
		// 					// if non gst
		// 					$mednefits_pecent = $trans->medi_percent / 100;
		// 					$fee = $temp_fee * $mednefits_pecent;
		// 				} else {
		// 					$fee = number_format((float)$trans->co_paid_amount, 2);
		// 				}
		// 			} else {
		// 				if(strrpos($trans->clinic_discount, '%')) {
		// 					$percentage = chop($trans->clinic_discount, '%');
		// 					if($trans->credit_cost > 0) {
		// 						$amount = $trans->credit_cost;
		// 					} else {
		// 						$amount = $trans->procedure_cost;
		// 					}

		// 					$total_percentage = $percentage + $trans->medi_percent;

		// 					$formatted_percentage = $total_percentage / 100;
		// 					$temp_fee = $amount / ( 1 - $formatted_percentage );
		// 					// if non gst
		// 					$mednefits_pecent = $trans->medi_percent / 100;
		// 					$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
		// 					$fee = $temp_mednefits_fee * $trans->gst_percent_value;
		// 				} else {
		// 					$fee = number_format((float)$trans->co_paid_amount, 2);
		// 				}
		// 			}
		// 			$total += $amount;

	 //    	}

	 //    	// $trans = $transaction->getTransactionById($value_2->transaction_id);
	 //    	// if($trans['deleted'] == 0 || $trans['deleted'] == "0") {
		//     // 	if($trans['procedure_cost'] <= 500) {
		//     // 		$amount = $trans['procedure_cost'] * $trans['medi_percent'];
		//     // 		$total += $amount;
		//     // 	} else {
		//     // 		$amount = 0;
		//     // 	}
		//     // 	$amount = 0;
		//     // 	// $statement_data['transactions'][$key]['transaction_date'] = $transaction->getTransactionDetails($value_2->transaction_id);
	 //    	// }
	 //    }

	 //    if((int)$statement_data['payment_record']['amount_paid'] != 0 || $statement_data['payment_record']['amount_paid'] != null || $statement_data['payment_record']['status'] == 1) {
	 //    	$due = 0;
	 //    } else {
	 //    	$due = $total;
	 //    }
	 //    $get_payment_details = $bank->getBankDetails($statement_data['payment_record']['clinic_id']);
	 //    $statement_data['bank_details'] = $get_payment_details;
	 //    $statement_data['total'] = $total;
	 //    $statement_data['amount_due'] = $due;
	 //    $statement_data['due_date']	= $invoice->getInvoiceClinic($statement_data['payment_record']['invoice_id']);

		return $transactions;
	}

	public function downloadClinicStatementPDF($id)
	{
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$statement = new StatementOfAccount();


		// if($statement_data['payment_record']['status'] == 0) {
		// 	return array(
		// 		'status'	=> 400,
		// 		'message'	=> 'No transaction found in the chosen month.'
		// 	);
		// }


		$transactions = [];
		$invoice_record_detail = new InvoiceRecordDetails();
		$clinic = new Clinic();
		$transaction = new Transaction();
		$bank = new PartnerDetails();

		$total = 0;

		$transactions['statement'] = $statement->getClinicStatement($id);
		$transactions['payment_record'] = $payment_record->getPaymentRecord($transactions['statement']['payment_record_id']);
		$invoice_record_data = \InvoiceRecord::where('invoice_id', $transactions['payment_record']['invoice_id'])->first();
		// return $invoice_record_data;
		$transactions['clinic'] = $clinic->ClinicDetails($transactions['payment_record']['clinic_id']);
		$check_payment_record = $payment_record->insertOrGet($transactions['payment_record']['invoice_id'], $transactions['payment_record']['clinic_id']);

		$details = [];
		$total = 0;
		$mednefits_total_fee = 0;
		$mednefits_total_credits = 0;
		$total_cash = 0;
		$total_procedure = 0;
		$total_percentage = 0;
		$total_transaction = 0;
		$total_fees = 0;
		$total_credits_transactions = 0;
		$total_cash_transactions = 0;
		$amount_due = null;
		$transaction_results = $invoice_record_detail->getTransaction($transactions['payment_record']['invoice_id']);
    // $transaction_data = [];
    // return $transaction_results;
		foreach ($transaction_results as $key => $value) {
			$trans = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.transaction_id', $value->transaction_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.paid', 1)
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.gst_percent_value')
			->first();

			if($trans) {
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$procedure_temp = "";
					$procedure = "";
					$procedure_ids = [];
					$mednefits_total_fee += $trans->credit_cost;
					if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
						$mednefits_total_credits += $trans->credit_cost;
					}

					if($trans->co_paid_status == 0) {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
								// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$fee = $temp_fee * $mednefits_pecent;
						} else {
							$fee = number_format((float)$trans->co_paid_amount, 2);
						}
					} else {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if($trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = $trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
								// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
							$fee = $temp_mednefits_fee * $trans->gst_percent_value;
						} else {
							$fee = number_format((float)$trans->co_paid_amount, 2);
						}
					}
					$total_fees += $fee;

					if($trans->credit_cost > 0) {
						$mednefits_credits = number_format($trans->credit_cost, 2);
						$cash = 0.00;
						$total_credits_transactions++;
					} else {
						$mednefits_credits = 00;
						$cash = number_format($trans->procedure_cost);
						$total_cash_transactions++;
					}

					if($trans->health_provider_done == 1 && $trans->credit_cost == 0 || $trans->health_provider_done == "1" && $trans->credit_cost == "0") {
						$total_cash += $trans->procedure_cost;
					}

					$mednefits_total_fee += $fee;

						// $temp = array(
						// 	'ClinicID'							=> $trans->ClinicID,
						// 	'NRIC'									=> $trans->NRIC,
						// 	'ProcedureID'						=> $procedure_id,
						// 	'UserID'								=> $trans->UserID,
						// 	'date_of_transaction'		=> date('d F Y', strtotime($trans->date_of_transaction)),
						// 	'paid'									=> $trans->paid,
						// 	'procedure_cost'				=> $trans->procedure_cost,
						// 	'services'							=> $procedure,
						// 	'transaction_id'				=> $trans->transaction_id,
						// 	'customer'							=> ucwords($trans->user_name),
						// 	'mednefits_fee'					=> number_format($fee, 2),
						// 	'discount'							=> $trans->clinic_discount,
						// 	'multiple_procedures' 	=> $trans->multiple_service_selection,
						// 	'health_provider'				=> $trans->health_provider_done,
						// 	'mednefits_credits'			=> number_format($mednefits_credits, 2),
						// 	'cash'									=> number_format($cash, 2),
						// 	'procedure_ids'					=> $procedure_ids,
						// 	'total'									=> $fee + $mednefits_credits
						// );
			   //  	array_push($transaction_data, $temp);
					$total_transaction++;
				}

			}

		}

		if($mednefits_total_fee > 0) {
			DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "true"]);
		} else {
			DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "false"]);
		}

		$get_payment_record = $payment_record->getPaymentRecord($check_payment_record);
		$get_payment_details = $bank->getBankDetails($transactions['payment_record']['clinic_id']);
		$transactions['payment_record'] = $get_payment_record;
		$transactions['bank_details'] = $get_payment_details;
		$paid_amount = (float)$transactions['payment_record']['amount_paid'];
    // $transactions['transaction_lists'] = $transaction_data;
		$invoice_record = array(
			'clinic_id'		=> $invoice_record_data->clinic_id,
			'invoice_id'	=> $invoice_record_data->invoice_id,
			'start_date'	=> date('Y-m-01', strtotime('+1 month', strtotime($invoice_record_data->start_date))),
			'end_date'		=> date('Y-m-t', strtotime('+1 month', strtotime($invoice_record_data->start_date))),
			'created_at'	=> $invoice_record_data->created_at,
			'updated_at'	=> $invoice_record_data->updated_at
		);
		$transactions['period'] = date('j M', strtotime($invoice_record_data->start_date)).' - '.date('j M Y', strtotime($invoice_record_data->end_date));
		$transactions['invoice_record'] = $invoice_record;
		$transactions['invoice_due'] = $invoice_record['end_date'];
		$transactions['total'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_fee'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_credits'] = number_format($mednefits_total_credits, 2);
		$transactions['total_cash'] = number_format($total_cash, 2);
		$transactions['clinic'] = DB::table('clinic')->where('ClinicID', $transactions['payment_record']['clinic_id'])->first();
    // $transactions['total_transaction'] = sizeof($transaction_data);
		$transactions['total_transaction'] = $total_transaction;
		$transactions['total_fees'] = number_format($total_fees, 2);
		$transactions['total_credits_transactions'] = $total_credits_transactions;
		$transactions['total_cash_transactions'] = $total_cash_transactions;
		$transactions['billing_name'] = $transactions['clinic']->billing_name ? ucwords($transactions['clinic']->billing_name) : $transactions['clinic']->Name;
		$transactions['billing_address'] = $transactions['clinic']->billing_address ? ucwords($transactions['clinic']->billing_address) : $transactions['clinic']->Address;

		$balance = $mednefits_total_fee - $paid_amount;

		if($balance > 0) {
			$amount_due = number_format($balance, 2);
		} else {
			$amount_due = number_format(0, 2);
		}
		$transactions['amount_due'] = $amount_due;
		$transactions['ending_balance'] = number_format($mednefits_total_fee - $get_payment_record->amount_paid, 2);

		// return $transactions;

		// return View::make('pdf-download.statement-of-account', $transactions);
		$pdf = PDF::loadView('pdf-download.statement-of-account', $transactions);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->download('Statement of Account.pdf');
	}

	public function getClinicStatementList()
	{
		$input = Input::all();
		$start = date('Y-m-d', strtotime($input['start']));
		$end = date('Y-m-d', strtotime($input['end']));

		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$statement = new StatementOfAccount();
		$statement_data = [];
		$statement_data['invoice'] = $invoice->getInvoiceByDate($start, $input['clinic_id']);
		$statement_data['statement'] = $statement->getClinicStatement($id);
		// $statement_data['payment_record'] =
	}

	public function testInvoice( )
	{
		// $pdf = PDF::loadView('invoice.purchase-plan-invoice')->setPaper('a4');
		// return $pdf->download('invoice.purchase-plan.pdf');
		return View::make('invoice.purchase-plan-invoice');
	}

	public function corporateInvoice($id)
	{

		$invoice = new CorporateInvoice();
		$get_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();

		if(!$get_active_plan) {
			return View::make('errors.503');
		}

		$corporate_business_contact = new CorporateBusinessContact();

		$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);

		if($check == 0) {


			if($check->new_head_count == 1 || $check->new_head_count == "1") {
				$due_date = date('Y-m-d', strtotime('+5 days', strtotime($get_active_plan->created_at)));
			} else {
				if($get_active_plan->cheque =="true" && $get_active_plan->credit == "false") {
					$due_date = date('Y-m-d', strtotime('-5 days', strtotime($get_active_plan->plan_start)));
				} else {
					$due_date = $get_active_plan->created_at;
				}
			}
			$check = DB::table('corporate_invoice')->count();
			$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);
			$data_invoice = array(
				'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
				'invoice_number'					=> 'OMC'.$invoice_number,
				'invoice_date'						=> $get_active_plan->created_at,
				'invoice_due'							=> $due_date,
				'employees'								=> $get_active_plan->employees,
				'customer_id'							=> $get_active_plan->customer_start_buy_id,
				'invoice_type'						=> 'invoice'
			);
			$invoice->createCorporateInvoice($data_invoice);
		}

		$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		$contact = $corporate_business_contact->getCorporateBusinessContact($get_active_plan->customer_start_buy_id);
		$corporate_business_info = new CorporateBusinessInformation();
		$business_info = $corporate_business_info->getCorporateBusinessInfo($get_active_plan->customer_start_buy_id);
		// return $get_invoice;
		$count_deleted_employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->count();
		if($contact->billing_contact == "false" || $contact->billing_contact == false) {
			$corporate_billing_contact = new CorporateBillingContact();
			$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($get_active_plan->customer_start_buy_id);
			$data['first_name'] = ucwords($result_corporate_billing_contact->first_name);
			$data['last_name']	= ucwords($result_corporate_billing_contact->last_name);
			$data['email']			= $result_corporate_billing_contact->work_email;
			$data['billing_contact_status'] = false;
		} else {
			$data['first_name'] = ucwords($contact->first_name);
			$data['last_name']	= ucwords($contact->last_name);
			$data['email']			= $contact->work_email;
			$data['billing_contact_status'] = true;
			$data['phone']     = $contact->phone;
		}

		if($contact->billing_address == "true") {
			$corporate_billing_address = new CorporateBillingAddress();
			$billing_address = $corporate_billing_address->getCorporateBillingAddress($get_active_plan->customer_start_buy_id);
			$data['address'] = $billing_address->billing_address;
			$data['postal'] = $billing_address->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = true;
		} else {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = false;
		}

		if($get_active_plan->status == "true") {
			$data['paid'] = true;
		} else {
			$data['paid'] = false;
		}

		$get_plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();
		$plan_start = $get_plan->plan_start;

		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		if($get_active_plan->account_type == 'stand_alone_plan') {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == 'insurance_bundle') {
			$data['plan_type'] = " Bundled Mednefits Care (Corporate)";
			$data['complimentary'] = TRUE;
		}

		self::checkPaymentHistory($id);

		$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();

		// check and format invoice number
		// return array('result' => self::formatInvoiceNumber($get_invoice->corporate_invoice_id));
		$data['invoice_number'] = $get_invoice->invoice_number;
		$data['invoice_date']		= $get_invoice->invoice_date;
		$data['invoice_due']		= $get_invoice->invoice_due;
		$data['number_employess'] = $get_invoice->employees + $count_deleted_employees;
		$data['plan_start']     = $get_active_plan->plan_start;
		if($get_active_plan->duration || $get_active_plan->duration != "") {
			$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
		} else {
			$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
		}
		$data['plan_end'] 			= date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));
		$data['price']          = number_format($get_invoice->individual_price, 2);
		$data['amount']					= number_format($data['number_employess'] * $get_invoice->individual_price, 2);
		$data['total']					= number_format($data['number_employess'] * $get_invoice->individual_price, 2);
		$data['amount_due']     = number_format($data['number_employess'] * $get_invoice->individual_price, 2);

		// return $data;
		return View::make('invoice.purchase-plan-invoice', $data);

		// $account_start = new CorporateBuyStart();
		// $account_start_result = $account_start->getAccountStart($check_active_plan->customer_start_buy_id);

		// if(!$account_start_result) {
		// 	return View::make('errors.503');
		// }
		// 	$invoice = new CorporateInvoice();
		// 	$active_plan = new CorporateActivePlan();
		// 	// $get_active_plan = $active_plan->getActivePlanData($id);
		// 	$get_active_plan = $check_active_plan;

		// if($account_start_result->cover_type == "team/corporate") {
		// 	$corporate_business_contact = new CorporateBusinessContact();

		// 	$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);

		// 	if($check == 0) {
		// 		if($get_active_plan->cheque =="true" && $get_active_plan->credit == "false") {
		// 			$due_date = date('Y-m-d', strtotime('-5 days', strtotime($get_active_plan->plan_start)));
		// 		} else {
		// 			$due_date = $get_active_plan->created_at;
		// 		}
		// 		$check = 10;
		// 		$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);
		// 		$data_invoice = array(
		// 			'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
		// 			'invoice_number'					=> 'OMC'.$invoice_number,
		// 			'invoice_date'						=> $get_active_plan->created_at,
		// 			'invoice_due'							=> $due_date
		// 		);
		// 		$invoice->createCorporateInvoice($data_invoice);
		// 	}

		// 	$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		// 	$contact = $corporate_business_contact->getCorporateBusinessContact($check_active_plan->customer_start_buy_id);
		// 	$corporate_business_info = new CorporateBusinessInformation();
		// 	$business_info = $corporate_business_info->getCorporateBusinessInfo($check_active_plan->customer_start_buy_id);
		// 	// return $get_invoice;
		// 	if($contact->billing_contact == "false" || $contact->billing_contact == false) {
		// 		$corporate_billing_contact = new CorporateBillingContact();
		// 		$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($check_active_plan->customer_start_buy_id);
		// 		$data['first_name'] = ucwords($result_corporate_billing_contact->first_name);
		// 		$data['last_name']	= ucwords($result_corporate_billing_contact->last_name);
		// 		$data['email']			= $result_corporate_billing_contact->work_email;
		// 		$data['billing_contact_status'] = false;
		// 	} else {
		// 		$data['first_name'] = ucwords($contact->first_name);
		// 		$data['last_name']	= ucwords($contact->last_name);
		// 		$data['email']			= $contact->work_email;
		// 		$data['billing_contact_status'] = true;
		// 		$data['phone']     = $contact->phone;
		// 	}

		// 	if($contact->billing_address == "true") {
		// 		$corporate_billing_address = new CorporateBillingAddress();
		// 		$billing_address = $corporate_billing_address->getCorporateBillingAddress($check_active_plan->customer_start_buy_id);
		// 		$data['address'] = $billing_address->billing_address;
		// 		$data['postal'] = $billing_address->postal_code;
		// 		$data['company'] = ucwords($business_info->company_name);
		// 		$data['billing_address_status'] = true;
		// 	} else {
		// 		$data['address'] = $business_info->company_address;
		// 		$data['postal'] = $business_info->postal_code;
		// 		$data['company'] = ucwords($business_info->company_name);
		// 		$data['billing_address_status'] = false;
		// 	}

		// 	if($account_start_result->cover_type == 'team/corporate') {
		// 		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
		// 	}

		// 	// return $get_active_plan;
		// 	// if($get_active_plan->cheque == "true" && $get_active_plan->credit == "false") {
		// 	// 	if($get_active_plan->paid_cheque == "true") {
		// 	// 		$data['paid'] = true;
		// 	// 	} else {
		// 	// 		$data['paid'] = false;
		// 	// 	}
		// 	// }
		// 	// else if($get_active_plan->cheque == "false" && $get_active_plan->credit == "true") {
		// 	if($get_active_plan->status == "true") {
		// 		$data['paid'] = true;
		// 	} else {
		// 		$data['paid'] = false;
		// 	}
		// 	// }
		// 	$data['invoice_number'] = $get_invoice->invoice_number;
		// 	$data['invoice_date']		= $get_invoice->invoice_date;
		// 	$data['invoice_due']		= $get_invoice->invoice_due;
		// 	$data['number_employess'] = $get_active_plan->employees;
		// 	$data['plan_start']     = $get_active_plan->plan_start;
		// 	$data['plan_end'] 			= $get_active_plan->end_date_policy;
		// 	$data['price']          = number_format(99, 2, '.', '');
		// 	$data['amount']					= number_format($get_active_plan->plan_amount, 2, '.', '');
		// 	$data['total']					= number_format($get_active_plan->plan_amount, 2, '.', '');
		// 	$data['amount_due']     = number_format($get_active_plan->plan_amount, 2, '.', '');
		// } else {

		// 	// $get_active_plan = $active_plan->getActivePlanData($id);
		// 	$get_active_plan = $check_active_plan;

		// 	$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		// 	if($check == 0) {
		// 		if($get_active_plan->cheque =="true" && $get_active_plan->credit == "false") {
		// 			$due_date = date('Y-m-d', strtotime('-5 days', strtotime($get_active_plan->plan_start)));
		// 			return $due_date;
		// 		} else {
		// 			$due_date = $get_active_plan->created_at;
		// 		}
		// 		$check = 10;
		// 		$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);
		// 		$data_invoice = array(
		// 			'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
		// 			'invoice_number'					=> 'OMC'.$invoice_number,
		// 			'invoice_date'						=> $get_active_plan->created_at,
		// 			'invoice_due'							=> $due_date
		// 		);
		// 		$invoice->createCorporateInvoice($data_invoice);
		// 	}

		// 	$personal_details = new CustomerPersonalDetails();
		// 	$personal_details_result = $personal_details->getCustomerPersonalDetailsData($check_active_plan->customer_start_buy_id);
		// 	// return $personal_details_result;

		// 	$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		// 	// return $get_invoice;
		// 	$data['plan_type'] = "Standalone Mednefits Care (Individual)";

		// 	if($get_active_plan->status == "true") {
		// 		$data['paid'] = true;
		// 	} else {
		// 		$data['paid'] = false;
		// 	}

		// 	$data['first_name'] = ucwords($personal_details_result->first_name);
		// 	$data['last_name']	= ucwords($personal_details_result->last_name);
		// 	$data['email']			= $personal_details_result->email;
		// 	$data['billing_contact_status'] = true;
		// 	$data['phone']     = $personal_details_result->mobile;

		// 	$data['address'] = $personal_details_result->address;
		// 	$data['postal'] = $personal_details_result->postal_code;
		// 	$data['company'] = '';
		// 	$data['billing_address_status'] = false;

		// 	$data['invoice_number'] = $get_invoice->invoice_number;
		// 	$data['invoice_date']		= $get_invoice->invoice_date;
		// 	$data['invoice_due']		= $get_invoice->invoice_due;
		// 	$data['number_employess'] = $get_active_plan->employees;
		// 	$data['plan_start']     = $get_active_plan->plan_start;
		// 	$data['plan_end'] 			= $get_active_plan->end_date_policy;
		// 	$data['price']          = number_format(99, 2, '.', '');
		// 	$data['amount']					= number_format($get_active_plan->plan_amount, 2, '.', '');
		// 	$data['total']					= number_format($get_active_plan->plan_amount, 2, '.', '');
		// 	$data['amount_due']     = number_format($get_active_plan->plan_amount, 2, '.', '');
		// }
		// return View::make('invoice.purchase-plan-invoice', $data);
	}

	public function checkPaymentHistory($id)
	{
		$payment_history = DB::table('customer_payment_history')->where('customer_active_plan_id', $id)->first();

		if(!$payment_history) {
          // create payment history
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();
			$plan = DB::table('customer_plan')->where('customer_plan_id', $active_plan->plan_id)->first();

			$first_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();

			if($first_plan->customer_active_plan_id == $id) {
				$status = 'started';
			} else {
				$status = 'added';
			}

			$employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $id)->count();

			$number_of_empoyees = $active_plan->employees + $employees;

			$data = array(
				'customer_buy_start_id'     => $active_plan->customer_start_buy_id,
				'customer_active_plan_id'   => $active_plan->customer_active_plan_id,
				'plan_start'                => $active_plan->plan_start,
				'status'                    => $status,
				'employees'                 => $number_of_empoyees,
				'amount'                    => $number_of_empoyees * 99,
				'stripe_transaction_id'     => Null
			);

			return \CorporatePaymentHistory::create($data);
		}
	}

	public function getCertificate($id)
	{

		$check_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();

		if(!$check_active_plan) {
			return View::make('errors.503');
		}

		$account_start = new CorporateBuyStart();
		$account_start_result = $account_start->getAccountStart($check_active_plan->customer_start_buy_id);

		if(!$account_start_result) {
			return View::make('errors.503');
		}
		$invoice = new CorporateInvoice();
		$active_plan = new CorporateActivePlan();
			// $get_active_plan = $active_plan->getActivePlanData($id);
		$get_active_plan = $check_active_plan;

		if($account_start_result->cover_type == "team/corporate") {
			$corporate_business_contact = new CorporateBusinessContact();
			// $check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
			$check = DB::table('customer_active_plan')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->first();

			if($check->new_head_count == 1 || $check->new_head_count == "1") {
				$due_date = date('Y-m-d', strtotime('+5 days', strtotime($get_active_plan->created_at)));
			} else {
				if($get_active_plan->cheque =="true" && $get_active_plan->credit == "false") {
					$due_date = date('Y-m-d', strtotime('-5 days', strtotime($get_active_plan->plan_start)));
				} else {
					$due_date = $get_active_plan->created_at;
				}
			}
			$check = DB::table('corporate_invoice')->count();
			$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);
			$data_invoice = array(
				'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
				'invoice_number'					=> 'OMC'.$invoice_number,
				'invoice_date'						=> $get_active_plan->created_at,
				'invoice_due'							=> $due_date,
				'employees'								=> $get_active_plan->employees,
				'customer_id'							=> $get_active_plan->customer_start_buy_id,
				'invoice_type'						=> 'invoice'
			);
			$invoice->createCorporateInvoice($data_invoice);

			$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
			$contact = $corporate_business_contact->getCorporateBusinessContact($check_active_plan->customer_start_buy_id);
			$corporate_business_info = new CorporateBusinessInformation();
			$business_info = $corporate_business_info->getCorporateBusinessInfo($check_active_plan->customer_start_buy_id);

			$data['email'] = $contact->work_email;
			if($contact['billing_status'] === "true" || $contact['billing_status'] === true) {
				$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
			} else {
				$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
				$data['name'] = ucwords($billing_contact->billing_name);
				$data['address'] = $billing_contact->billing_address;
			}
			$data['phone']     = $contact->phone;
			if($contact->billing_status === "true") {
				// $corporate_billing_address = new CorporateBillingAddress();
				// $billing_address = $corporate_billing_address->getCorporateBillingAddress($get_active_plan->customer_start_buy_id);
				$data['address'] = $business_info->company_address;
				$data['postal'] = $business_info->postal_code;
				$data['company'] = ucwords($business_info->company_name);
				// $data['billing_address_status'] = true;
			} else {
				$data['postal'] = $business_info->postal_code;
				$data['company'] = ucwords($business_info->company_name);
				// $data['billing_address_status'] = false;
			}


			if($get_active_plan->account_type == 'stand_alone_plan') {
				$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
				$data['complimentary'] = FALSE;
			} else if($get_active_plan->account_type == 'insurance_bundle') {
				$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
				$data['complimentary'] = TRUE;
			} else if($get_active_plan->account_type == 'lite_plan') {
				$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
				$data['complimentary'] = FALSE;
			}

			if($get_active_plan->status == "true") {
				$data['paid'] = true;
			} else {
				$data['paid'] = false;
			}

			self::checkPaymentHistory($id);
			$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();

			if($get_active_plan->duration || $get_active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}

			$data['invoice_number'] = $get_invoice->invoice_number;
			$data['invoice_date']		= $get_invoice->invoice_date;
			$data['invoice_due']		= $get_invoice->invoice_due;
			$data['next_billing']   = date('Y-m-d', strtotime('-30 days', strtotime($get_invoice->invoice_due)));
			$data['number_employess'] = $get_active_plan->employees;
			$data['plan_start']     = $get_active_plan->plan_start;
			$data['plan_end'] 			= date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));
			$data['price']          = number_format(99, 2, '.', '');
			$data['amount']					= number_format($data['number_employess'] * $get_invoice->individual_price, 2);
			$data['total']					= number_format($data['number_employess'] * $get_invoice->individual_price, 2);
			$data['amount_due']     = number_format($data['number_employess'] * $get_invoice->individual_price, 2);
		} else {

			// $get_active_plan = $active_plan->getActivePlanData($id);
			$get_active_plan = $check_active_plan;

			$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
			if($check == 0) {
				if($get_active_plan->cheque =="true" && $get_active_plan->credit == "false") {
					$due_date = date('Y-m-d', strtotime('-5 days', strtotime($get_active_plan->plan_start)));
				} else {
					$due_date = $get_active_plan->created_at;
				}
				$check = 10;
				$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);
				$data_invoice = array(
					'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
					'invoice_number'					=> 'OMC'.$invoice_number,
					'invoice_date'						=> $get_active_plan->created_at,
					'invoice_due'							=> $due_date
				);
				$invoice->createCorporateInvoice($data_invoice);
			}

			$personal_details = new CustomerPersonalDetails();
			$personal_details_result = $personal_details->getCustomerPersonalDetailsData($check_active_plan->customer_start_buy_id);
			// return $personal_details_result;

			$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
			// return $get_invoice;
			$data['plan_type'] = "Standalone Mednefits Care (Individual)";

			if($get_active_plan->status == "true") {
				$data['paid'] = true;
			} else {
				$data['paid'] = false;
			}

			$data['first_name'] = ucwords($personal_details_result->first_name);
			$data['last_name']	= ucwords($personal_details_result->last_name);
			$data['email']			= $personal_details_result->email;
			$data['billing_contact_status'] = true;
			$data['phone']     = $personal_details_result->mobile;

			$data['address'] = $personal_details_result->address;
			$data['postal'] = $personal_details_result->postal_code;
			$data['company'] = '';
			$data['billing_address_status'] = false;

			$data['invoice_number'] = $get_invoice->invoice_number;
			$data['invoice_date']		= $get_invoice->invoice_date;
			$data['invoice_due']		= $get_invoice->invoice_due;
			$data['next_billing']   = date('Y-m-d', strtotime('-30 days', strtotime($get_invoice->invoice_due)));
			$data['number_employess'] = $get_active_plan->employees;
			$data['plan_start']     = $get_active_plan->plan_start;
			$data['plan_end'] 			= $get_active_plan->end_date_policy;
			$data['price']          = number_format(99, 2, '.', '');
			$data['amount']					= number_format($get_active_plan->plan_amount, 2, '.', '');
			$data['total']					= number_format($get_active_plan->plan_amount, 2, '.', '');
			$data['amount_due']     = number_format($get_active_plan->plan_amount, 2, '.', '');
		}

		// return View::make('invoice.certificate', $data);
		// return View::make('pdf-download.hr-certificate', $data);
		$pdf = PDF::loadView('pdf-download.hr-certificate', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->download($data['invoice_number'].' Certificate - '.time().'.pdf');
	}

	public function formatInvoiceNumber($id)
	{
		$active_plans = DB::table('customer_active_plan')->where('customer_start_buy_id', $id)->get();
		$format = [];

		foreach ($active_plans as $key => $plan) {
			$check = 10;
			$temp_invoice_number = str_pad($check + $key + 1, 6, "0", STR_PAD_LEFT);
			$invoice_number = 'OMC'.$temp_invoice_number.'A';
			$temp = array(
				'invoice_number' => ++$invoice_number
			);

			array_push($format, $temp);
		}

		return $format;
	}

	public function validateDate($date)
	{
		$d = \DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}


	public function getReceipt( )
	{
		$input = Input::all();
  	// return $input['invoice_id'];
		if(empty($input['invoice_id']) || $input['invoice_id'] == null) {
			return View::make('errors.503');
		}

		$id = $input['invoice_id'];

		$invoice = CorporateInvoice::where('corporate_invoice_id', $id)->first();

		if(!$invoice) {
			return View::make('errors.503');
		}

		$get_active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $invoice->customer_active_plan_id)
		->first();

		if(!$get_active_plan) {
			return View::make('errors.503');
		}

		$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();


		$data['email'] = $contact->work_email;
		$data['phone']     = $contact->phone;

		if($contact->billing_status === "true" || $contact->billing_status === true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
			$data['name'] = ucwords($billing_contact->billing_name);
			$data['address'] = $billing_contact->billing_address;
		}

		if($contact->billing_status === "true") {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
		} else {
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
		}


		$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();
		$plan_start = $plan->plan_start;

		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		$data['complimentary'] = FALSE;
		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";

		if($get_active_plan->account_type == "stand_alone_plan") {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['account_type'] = "Pro Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "insurance_bundle") {
			$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
			$data['account_type'] = "Insurance Bundle";
			$data['complimentary'] = TRUE;
		} else if($get_active_plan->account_type == "trial_plan") {
			$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Trial Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "lite_plan") {
			$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Lite Plan";
			$data['complimentary'] = FALSE;
		}

		$data['invoice_number'] = $invoice->invoice_number;
		$data['invoice_date']		= date('F d, Y', strtotime($invoice->invoice_date));
		$data['invoice_due']		= date('F d, Y', strtotime($invoice->invoice_due));
		$data['number_employess'] = $invoice->employees;
		$data['plan_start']     = date('F d, Y', strtotime($get_active_plan->plan_start));

		if($get_active_plan->new_head_count == 0) {
			if((int)$invoice->plan_extention_enable == 1) {
				$extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();
				if($extension) {
					if($extension->duration || $extension->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$extension->duration, strtotime($extension->plan_start)));
						$data['duration'] = $extension->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					if($extension->account_type == "stand_alone_plan") {
						$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
						$data['account_type'] = "Pro Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "insurance_bundle") {
						$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
						$data['account_type'] = "Insurance Bundle";
						$data['complimentary'] = TRUE;
					} else if($extension->account_type == "trial_plan") {
						$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Trial Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "lite_plan") {
						$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Lite Plan";
						$data['complimentary'] = FALSE;
					}

					$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();

					if((int)$extension->paid == 1) {
						$data['paid'] = true;
						$payment = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;

							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}

						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					$payment = DB::table('customer_cheque_logs')
					->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->first();

					if($get_active_plan->paid == "true") {
						$data['paid'] = true;
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;
						}
					} else {
						$data['paid'] = false;
					}

					if($get_active_plan->paid == "true") {
						if($payment) {
							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}
						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}

					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
				}
			} else {

				$data['price']          = number_format($invoice->individual_price, 2);
				$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
				$amount_due = $data['number_employess'] * $invoice->individual_price;
				$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

				$payment = DB::table('customer_cheque_logs')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();

				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
						} else {
							$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
						}
						$data['notes']		  = $payment->remarks;
					}
				} else {
					$data['paid'] = false;
				}

				if($get_active_plan->paid == "true") {
					if($payment) {
						$temp_amount_due = $amount_due - $payment->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = "0.00";
						} else {
							$data['amount_due'] = number_format($temp_amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					$data['amount_due']     = number_format($amount_due, 2);
				}

				if($get_active_plan->duration || $get_active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
					$data['duration'] = $get_active_plan->duration;
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
					$data['duration'] = '12 months';
				}
			}
		} else {
			if((int)$invoice->plan_extention_enable == 1) {
				$extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();
				if($extension) {
					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					if($extension->account_type == "stand_alone_plan") {
						$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
						$data['account_type'] = "Pro Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "insurance_bundle") {
						$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
						$data['account_type'] = "Insurance Bundle";
						$data['complimentary'] = TRUE;
					} else if($extension->account_type == "trial_plan") {
						$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Trial Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "lite_plan") {
						$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Lite Plan";
						$data['complimentary'] = FALSE;
					}

					$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();

					if((int)$extension->paid == 1) {
						$data['paid'] = true;
						$payment = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;

							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}

						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = number_format($amount_due, 2);
					}

				} else {
					$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
					$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
					$calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $end_plan_date);
					$data['price']          = number_format($calculated_prices, 2);
					$amount_due = $data['number_employess'] * $calculated_prices;
					$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
					$data['total']					= number_format($data['number_employess'] * $calculated_prices, 2);
					$data['duration'] = $get_active_plan->duration;
				}
			} else {

				$payment = DB::table('customer_cheque_logs')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();

				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
						} else {
							$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
						}
						$data['notes']		  = $payment->remarks;
					}
				} else {
					$data['paid'] = false;
				}

				if($get_active_plan->paid == "true") {
					if($payment) {
						$temp_amount_due = $amount_due - $payment->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = "0.00";
						} else {
							$data['amount_due'] = number_format($temp_amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					$data['amount_due']     = number_format($amount_due, 2);
				}

				$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
				$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
				$calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $end_plan_date);
				$data['price']          = number_format($calculated_prices, 2);
				$amount_due = $data['number_employess'] * $calculated_prices;
				$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
				$data['total']					= number_format($data['number_employess'] * $calculated_prices, 2);
				$data['duration'] = $get_active_plan->duration;
			}

		}

		$data['customer_active_plan_id'] = $get_active_plan->customer_active_plan_id;
		$data['plan_end'] 			= date('F d, Y', strtotime('-1 day', strtotime($end_plan_date)));

		if($get_active_plan->cheque == "true" && $get_active_plan->credit == "false") {
			if($get_active_plan->paid_cheque == "true" || $get_active_plan->paid == "true") {
				$data['paid'] = true;
				$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->first();
				if($payment) {
					$data['paid_date'] = date('F d, Y', strtotime($payment->date_received));
					$data['payment_remarks'] = $payment->remarks;
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CHEQUE";
		} else if($get_active_plan->cheque == "false" && $get_active_plan->credit == "true") {
			if($get_active_plan->paid == "true") {
				$data['paid'] = true;
				$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->first();
				if($payment) {
					$data['paid_date'] = $payment->date_received;
					$data['payment_remarks'] = $payment->remarks;
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CREDIT CARD";
		}

		$data['paid_amount'] = $data['amount'];

	// return View::make('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $data);
		$pdf = PDF::loadView('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->download($data['invoice_number'].' Receipt - '.time().'.pdf');
	}

	public function getoldReceipt($id)
	{

		$invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $id)->first();
		if(!$invoice) {
			return View::make('errors.503');
		}

		// 

		$get_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();
		// return json_encode($get_active_plan);
		if(!$get_active_plan) {
			return View::make('errors.503');
		}

		$account_start = new CorporateBuyStart();
		$account_start_result = $account_start->getAccountStart($get_active_plan->customer_start_buy_id);

		if(!$account_start_result) {
			return View::make('errors.503');
		}

		$corporate_business_contact = new CorporateBusinessContact();
		$invoice = new CorporateInvoice();
		$active_plan = new CorporateActivePlan();

		$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);

		if($check == 0) {
			$check = 10;
			$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);
			$data_invoice = array(
				'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
				'invoice_number'					=> 'OMC'.$invoice_number,
				'invoice_date'						=> $get_active_plan->created_at,
				'invoice_due'							=> $get_active_plan->created_at,
				'employees'								=> $get_active_plan->employees,
				'customer_id'							=> $account_start_result->customer_buy_start_id,
				'invoice_type'						=> 'invoice'
			);
			$invoice->createCorporateInvoice($data_invoice);
		}

		$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		$contact = $corporate_business_contact->getCorporateBusinessContact($get_active_plan->customer_start_buy_id);
		$corporate_business_info = new CorporateBusinessInformation();
		$business_info = $corporate_business_info->getCorporateBusinessInfo($get_active_plan->customer_start_buy_id);

		if($contact->billing_contact == "false" || $contact->billing_contact == false) {
			$corporate_billing_contact = new CorporateBillingContact();
			$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($get_active_plan->customer_start_buy_id);
			$data['first_name'] = ucwords($result_corporate_billing_contact->first_name);
			$data['last_name']	= ucwords($result_corporate_billing_contact->last_name);
			$data['email']			= $result_corporate_billing_contact->work_email;
			$data['billing_contact_status'] = false;
		} else {
			$data['first_name'] = ucwords($contact->first_name);
			$data['last_name']	= ucwords($contact->last_name);
			$data['email']			= $contact->work_email;
			$data['billing_contact_status'] = true;
			$data['phone']     = $contact->phone;
		}

		if($contact->billing_address == "true") {
			$corporate_billing_address = new CorporateBillingAddress();
			$billing_address = $corporate_billing_address->getCorporateBillingAddress($get_active_plan->customer_start_buy_id);
			$data['address'] = $billing_address->billing_address;
			$data['postal'] = $billing_address->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = true;
		} else {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = false;
		}

		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
		$count_deleted_employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->count();

		if($get_active_plan->cheque == "true" && $get_active_plan->credit == "false") {
			if($get_active_plan->paid_cheque == "true") {
				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					$validate = self::validateDate($get_active_plan->paid_date);
					if($validate) {
						$data['paid_date'] = $get_active_plan->paid_date;
					} else {
						$data['paid_date'] = $get_active_plan->created_at;
					}
				} else {
					// $data['paid'] = false;
					return array(
						'status'	=> FALSE,
						'message'	=> 'Not yet paid.'
					);
				}
			} else {
				// $data['paid'] = false;
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CHEQUE";
		} else if($get_active_plan->cheque == "false" && $get_active_plan->credit == "true") {
			if($get_active_plan->paid == "true") {
				$data['paid'] = true;
				$data['paid_date'] = $get_active_plan->created_at;
			} else {
				// $data['paid'] = false;
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CREDIT CARD";
		}

		$data['invoice_number'] = $get_invoice->invoice_number;
		$data['invoice_date']		= $get_invoice->invoice_date;
		$data['invoice_due']		= $get_invoice->invoice_due;
		$data['next_billing']   = date('Y-m-d', strtotime('-30 days', strtotime($get_invoice->invoice_due)));
		$data['number_employess'] = $get_active_plan->employees + $count_deleted_employees;
		$data['plan_start']     = $get_active_plan->plan_start;
		$data['plan_end'] 			= $get_active_plan->end_date_policy;
		$data['price']          = number_format($get_invoice->individual_price, 2);
		$data['amount']					= number_format($data['number_employess'] * $get_invoice->individual_price, 2);
		$data['total']					= number_format($data['number_employess'] * $get_invoice->individual_price, 2);
		$data['amount_due']     = number_format($data['number_employess'] * $get_invoice->individual_price, 2);

		$check_log = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();

		if($check_log) {
			$data['paid_amount'] = number_format($check_log->paid_amount, 2);
			$data['payment_remarks'] = $check_log->remarks;
			$data['paid_date'] = $check_log->date_received;
		} else {
			$data['paid_amount'] = $data['total'];
			$data['payment_remarks'] = NULL;
		}

		// return View::make('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $data);
		$pdf = PDF::loadView('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->download($data['invoice_number'].' Certificate - '.time().'.pdf');
	}

	public function getHrStatement($id)
	{

		$data['status'] = true;
		return View::make('invoice.hr-statement-invoice', $data);
	}


	public function generateMonthlyInvoice( )
	{
		
		$input = Input::all();
		$result_data = [];

		if(!empty($input['start'])) {
			$start_date = date('Y-m-01', strtotime($input['start']));
			$end_date = date('Y-m-t', strtotime($input['start']));
		} else {
			$start_date = date('Y-m-01', strtotime('-1 month'));
			$end_date = date('Y-m-t', strtotime('-1 month'));
		}
		
		if(!empty($input['clinic_id']) && $input['clinic_id'] != null) {
			$clinics = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->where('Active', 1)->get();
		} else {
			$clinics = DB::table('clinic')->where('Active', 1)->get();
		}

		// return $clinics;
		$total_success_generate = 0;
		$total_fail_generate = 0;
		// return $start_date.' - '.$end_date;

		$clinic = new Clinic();
		$transaction = new Transaction();
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$invoice_record_detail = new InvoiceRecordDetails();
		$doctor = new Doctor();
		$procedure = new ClinicProcedures();
		$user = new User();
		$bank = new PartnerDetails();
		$invoice_records = [];


		foreach ($clinics as $key => $clinic) {
			$input = array(
				'start_date'	=> $start_date,
				'end_date'		=> $end_date,
				'clinic_id'		=> $clinic->ClinicID
			);

			$check_transaction = $transaction->checkTransaction($clinic->ClinicID, $input);
			if($check_transaction == 0) {
				$temp = array(
					'status'	=> 400,
					'message'	=> "No transaction found in the chosen month."
				);
				$total_fail_generate++;
				array_push($result_data, $temp);
			} else {
				$check_invoice = $invoice->checkInvoice($input);
				if($check_invoice) {
					$invoice_id = $check_invoice['invoice_id'];
					$invoice_data =  $check_invoice;
					$transaction_list = $transaction->getDateTransaction($check_invoice);
					$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $check_invoice['invoice_id'], $check_invoice['clinic_id']);
					$check_payment_record = $payment_record->insertOrGet($check_invoice['invoice_id'], $clinic->ClinicID);
				} else {
					$result_create = $invoice->createInvoice($input);
					$invoice_data = $result_create;
					$invoice_id = $result_create->id;
					$transaction_list = $transaction->getDateTransaction($result_create);
					$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $result_create->id, $clinic->ClinicID);
					$check_payment_record = $payment_record->insertOrGet($result_create->id, $clinic->ClinicID);
		    	// $check_payment_record['payment_record_id'] = $check_payment_record->id;
				}

				$invoice_data = self::sendClinicIvoice($invoice_id);
				if($invoice_data) {
					// send email for clinic invoice
					try {
						$total_success_generate++;
						$email_to = "medicloud.finance@receiptbank.me";
						// $email_to = "allan.alzula.work@gmail.com";
						$email = [];

						$email['emailSubject'] = 'MEDNEFITS CLINIC INVOICE';
						$email['emailName'] = 'Mednefits';
						$email['emailPage'] = 'email-templates.blank';
						$email['emailTo'] = $email_to;
						$email['data'] = $invoice_data;
						// return $email;
						EmailHelper::sendEmailClinicInvoiceFile($email);
						array_push($result_data, $invoice_data);
					} catch(Exception $e) {
						array_push($result_data, ['res' => $e->getMessage()]);
						// return $e->getMessage();
					}

				}
				try {
					$invoice_status = DB::table('invoice_record')->where('invoice_id', $invoice_id)->first();
					$admin_logs = array(
						'admin_id'  => null,
						'type'      => 'clinic_generate_invoice_system_generate',
						'data'      => SystemLogLibrary::serializeData($invoice_status)
					);
					SystemLogLibrary::createAdminLog($admin_logs);
				} catch(Exception $e) {
					// return $e->getMessage();
					array_push($result_data, ['res' => $e->getMessage()]);
				}
			}

		}


		$currenttime = StringHelper::CurrentTime();
		$emailDdata['emailName']= 'Mednefits Booking Automatic Invoice Generate';
		$emailDdata['emailPage']= 'email-templates.invoice-generate';
		$emailDdata['emailTo']= 'info@medicloud.sg';
		$emailDdata['emailSubject'] = "Cron for Invoice Generate";
		$emailDdata['actionDate'] = date('d-m-Y');
		$emailDdata['actionTime'] = $currenttime;
		$emailDdata['total_success'] = $total_success_generate;
		$emailDdata['total_fail'] = $total_fail_generate;
		$emailDdata['totalRecords'] = count($result_data);
		EmailHelper::sendEmailDirect($emailDdata);

		$emailDdata['emailTo']= 'developer.mednefits@gmail.com';
		EmailHelper::sendEmailDirect($emailDdata);
		return $result_data;

	}

	public function sendClinicIvoice($id)
	{
		$invoice_record_detail = new InvoiceRecordDetails();
		$invoice_data = DB::table('invoice_record')->where('invoice_id', $id)->first();

		if(!$invoice_data) {
			return false;
		}

		$transaction = new Transaction();
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$doctor = new Doctor();
		$procedure = new ClinicProcedures();
		$user = new User();
		$bank = new PartnerDetails();

		$transactions = [];
		$transaction_data = [];
		$details = [];
		$total = 0;
		$mednefits_total_fee = 0;
		$mednefits_total_credits = 0;
		$total_cash = 0;
		$total_procedure = 0;
		$total_percentage = 0;
		$total_transaction = 0;
		$total_fees = 0;
		$total_credits_transactions = 0;
		$total_cash_transactions = 0;

		$transaction_results = $invoice_record_detail->getTransaction($id);

		foreach ($transaction_results as $key => $value) {
			$trans = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.transaction_id', $value->transaction_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.paid', 1)
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.gst_percent_value', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount')
			->first();

			if($trans) {
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$procedure_temp = "";
					$procedure = "";
					$procedure_ids = [];
					$mednefits_total_fee += (float)$trans->credit_cost;
					if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
						$mednefits_total_credits += (float)$trans->credit_cost;
					}
					if($trans->co_paid_status == 0) {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if((float)$trans->credit_cost > 0) {
								$amount = (float)$trans->credit_cost;
							} else {
								$amount = (float)$trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
							// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$fee = $temp_fee * $mednefits_pecent;
						} else {
							if((int)$trans->peak_hour_status == 1) {
								$fee = (float)$trans->peak_hour_amount;
							} else {
								$fee = (float)$trans->co_paid_amount;
							}
						}
					} else {
						if(strrpos($trans->clinic_discount, '%')) {
							$percentage = chop($trans->clinic_discount, '%');
							if((float)$trans->credit_cost > 0) {
								$amount = $trans->credit_cost;
							} else {
								$amount = (float)$trans->procedure_cost;
							}

							$total_percentage = $percentage + $trans->medi_percent;

							$formatted_percentage = $total_percentage / 100;
							$temp_fee = $amount / ( 1 - $formatted_percentage );
							// if non gst
							$mednefits_pecent = $trans->medi_percent / 100;
							$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
							$fee = $temp_mednefits_fee * $trans->gst_percent_value;
						} else {
							if((int)$trans->peak_hour_status == 1) {
								$fee = (float)$trans->peak_hour_amount;
							} else {
								$fee = (float)$trans->co_paid_amount;
							}
						}
					}
					$total_fees += $fee;

					if($trans->credit_cost > 0) {
						$mednefits_credits = (float)$trans->credit_cost;
						$cash = 0.00;
						$total_credits_transactions++;
					} else {
						if(strripos($trans->procedure_cost, '$') !== false) {
							$temp_cost = explode('$', $trans->procedure_cost);
		            // $cost = number_format($temp_cost[1]);
							$cost = $temp_cost[1];
						} else {
		            // $cost = number_format($trans->procedure_cost, 2);
							$cost = floatval($trans->procedure_cost);
						}
						$mednefits_credits = 00;
						$cash = $cost;
						$total_cash_transactions++;
					}

					if((int)$trans->health_provider_done == 1 && (int)$trans->credit_cost == 0) {
						if(strripos($trans->procedure_cost, '$') !== false) {
							$temp_cost = explode('$', $trans->procedure_cost);
		            // $cost = number_format($temp_cost[1]);
							$cost = $temp_cost[1];
						} else {
		            // $cost = number_format($trans->procedure_cost, 2);
							$cost = floatval($trans->procedure_cost);
						}
						$total_cash += $cost;
					}

					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();

					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$temp = array(
						'transaction_id'    		=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'ClinicID'							=> $trans->ClinicID,
						'NRIC'									=> $trans->NRIC,
					// 'ProcedureID'						=> $procedure_id,
						'UserID'								=> $trans->UserID,
						'date_of_transaction'		=> date('d F Y', strtotime($trans->date_of_transaction)),
						'paid'									=> $trans->paid,
						'procedure_cost'				=> (float)$trans->procedure_cost,
						'services'							=> $procedure,
						'customer'							=> ucwords($trans->user_name),
						'mednefits_fee'					=> number_format($fee, 2),
						'discount'							=> $trans->clinic_discount,
						'multiple_procedures' 	=> $trans->multiple_service_selection,
						'health_provider'				=> $trans->health_provider_done,
						'mednefits_credits'			=> number_format($mednefits_credits, 2),
						'cash'									=> number_format($cash, 2),
						'procedure_ids'					=> $procedure_ids,
						'total'									=> number_format($fee + $mednefits_credits, 2),
						'currency_type'					=> "SGD"
					);
					array_push($transaction_data, $temp);

					$mednefits_total_fee += $fee;
					$total_transaction++;
				}
			}
		}

		if($total_transaction == 0) {
			return false;
		}

		$get_payment_record = \PaymentRecord::where('invoice_id', $id)->first();
		$get_payment_details = $bank->getBankDetails($get_payment_record->clinic_id);
		$transactions['payment_record'] = $get_payment_record;
		$transactions['bank_details'] = $get_payment_details;
		$paid_amount = (float)$transactions['payment_record']['amount_paid'];

		$end_date = date('Y-m-t', strtotime('+1 month', strtotime($invoice_data->start_date)));
		$invoice_record = array(
			'clinic_id'		=> $invoice_data->clinic_id,
			'invoice_id'	=> $invoice_data->invoice_id,
			'start_date'	=> date('F d, Y', strtotime('+1 month', strtotime($invoice_data->start_date))),
			'end_date'		=> date('F d, Y', strtotime($end_date)),
			'created_at'	=> $invoice_data->created_at,
			'updated_at'	=> $invoice_data->updated_at
		);
		$transactions['period'] = date('j M', strtotime($invoice_data->start_date)).' - '.date('j M Y', strtotime($invoice_data->end_date));
		$transactions['invoice_record'] = $invoice_record;
		$transactions['invoice_due'] = $invoice_record['end_date'];
		$transactions['total'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_fee'] = number_format($mednefits_total_fee, 2);
		$transactions['mednefits_credits'] = number_format($mednefits_total_credits, 2);
		$transactions['total_cash'] = number_format($total_cash, 2);
		$transactions['clinic'] = DB::table('clinic')->where('ClinicID', $invoice_data->clinic_id)->first();
    // $transactions['total_transaction'] = sizeof($transaction_data);
		$transactions['total_transaction'] = $total_transaction;
		$transactions['total_fees'] = number_format($total_fees, 2);
		$transactions['total_credits_transactions'] = $total_credits_transactions;
		$transactions['total_cash_transactions'] = $total_cash_transactions;
		$transactions['currency_type'] = "SGD";
		$transactions['transactions'] = $transaction_data;
		$balance = $mednefits_total_fee - $paid_amount;

		if($balance > 0) {
			$amount_due = $balance;
		} else if($balance < 0) {
			$amount_due = 0;
		} else {
			$amount_due = $balance;
		}

		$transactions['amount_due'] = number_format($balance, 2);
		$transactions['invoice_number'] = $get_payment_record->invoice_number;
		return $transactions;
    // return View::make('pdf-download.clinic_invoice', $transactions);
		// $pdf = PDF::loadView('pdf-download.clinic_invoice', $transactions);
		// $pdf->getDomPDF()->get_option('enable_html5_parser');
		// $pdf->setPaper('A4', 'portrait');

		return $pdf->stream($get_payment_record->invoice_number.' - '.time().'.pdf');
	}

	public function createClinicInvoice( )
	{

		$input = Input::all();

		$clinics = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->where('Active', 1)->get();
		// return json_encode($clinic);
		$result_data = [];
		$start_date = date('Y-m-d', strtotime($input['start_date']));
		$end_date = date('Y-m-d', strtotime($input['end_date']));
		$total_success_generate = 0;
		$total_fail_generate = 0;
		// return $start_date.' - '.$end_date;

		$clinic = new Clinic();
		$transaction = new Transaction();
		$invoice = new InvoiceRecord();
		$payment_record = new PaymentRecord();
		$invoice_record_detail = new InvoiceRecordDetails();
		$doctor = new Doctor();
		$procedure = new ClinicProcedures();
		$user = new User();
		$bank = new PartnerDetails();
		$invoice_records = [];


		foreach ($clinics as $key => $clinic) {
			$input = array(
				'start_date'	=> $start_date,
				'end_date'		=> $end_date,
				'clinic_id'		=> $clinic->ClinicID
			);

			$check_transaction = $transaction->checkTransaction($clinic->ClinicID, $input);
	    // return $check_transaction;
			if($check_transaction == 0) {
				$temp = array(
					'status'	=> 400,
					'message'	=> "No transaction found in the chosen month."
				);
				$total_fail_generate++;
				array_push($result_data, $temp);
			} else {
				$check_invoice = $invoice->checkInvoice($input);
				if($check_invoice) {
					$invoice_id = $check_invoice['invoice_id'];
					$invoice_data =  $check_invoice;
					$transaction_list = $transaction->getDateTransaction($check_invoice);
					$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $check_invoice['invoice_id'], $check_invoice['clinic_id']);
					$check_payment_record = $payment_record->insertOrGet($check_invoice['invoice_id'], $clinic->ClinicID);
				} else {
					$result_create = $invoice->createInvoice($input);
					$invoice_data = $result_create;
					$invoice_id = $result_create->id;
					$transaction_list = $transaction->getDateTransaction($result_create);
					$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $result_create->id, $clinic->ClinicID);
					$check_payment_record = $payment_record->insertOrGet($result_create->id, $clinic->ClinicID);
		    	// $check_payment_record['payment_record_id'] = $check_payment_record->id;
				}

				$transactions = [];
				$details = [];
				$total = 0;
				$total_procedure = 0;
				$total_percentage = 0;
				$transaction_results = $invoice_record_detail->getTransaction($invoice_id);
				foreach ($transaction_results as $key => $value) {
					$trans['transaction'] = $transaction->getTransactionById($value->transaction_id);
					$procedure_name = $procedure->ClinicProcedureByID($trans['transaction']['ProcedureID']);
					$trans['procedure'] = $procedure_name ? $procedure_name->Name : '';
					$customer = $user->getUserProfile($trans['transaction']['UserID']);
					$trans['customer'] = $customer ? $customer->Name : 'Mednefits User';
					if($trans['transaction']['procedure_cost'] <= 500) {
						if($trans['transaction']['co_paid_status'] == 1) {
							if($trans['transaction']['co_paid_amount'] > 0) {
								$amount = $trans['transaction']['credit_cost'] + $trans['transaction']['co_paid_amount'];
							} else {
								$amount = $trans['transaction']['credit_cost'] + 13.91;
							}
							$trans['discount_value'] = '$13.91';
						} else {
							$clinic_cost = $trans['transaction']['procedure_cost'] * $trans['transaction']['medi_percent'];
							$amount = $trans['transaction']['credit_cost'] + $clinic_cost;
							$trans['discount_value'] = $trans['transaction']['medi_percent'] * 100 .'%';
						}

						$total += $amount;

						$total_percentage += $trans['transaction']['medi_percent'];
						$total_procedure += $trans['transaction']['procedure_cost'];
					} else {
						$amount = 0;
					}


					$trans['calculations'] = $amount;
					$transactions['items'][] = $trans;
					$amount = 0;
				}
				if($total > 0) {
					DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "true"]);
					$total_success_generate++;
				} else {
					$total_fail_generate++;
					DB::table('payment_record')->where('payment_record_id', $check_payment_record)->update(['has_total' => "false"]);
				}
				$get_payment_record = $payment_record->getPaymentRecord($check_payment_record);
				$get_payment_details = $bank->getBankDetails($clinic->ClinicID);
				$transactions['payment_record'] = $get_payment_record;
				$transactions['bank_details'] = $get_payment_details;
				$transactions['invoice_record'] = $invoice_data;
				$transactions['invoice_due'] = date('Y-m-d', strtotime('+1 month', strtotime($invoice_data['end_date'])));
				$transactions['total'] = $total;
				$transactions['clinic'] = $clinic;
				array_push($result_data, $transactions);
			}

		}

		return $result_data;
		// $currenttime = StringHelper::CurrentTime();

  //   $emailDdata['emailName']= 'Mednefits Booking Automatic Invoice Generate';
  //   $emailDdata['emailPage']= 'email-templates.invoice-generate';
  //   $emailDdata['emailTo']= 'info@medicloud.sg';
  //   $emailDdata['emailSubject'] = "Cron for Invoice Generate";
  //   $emailDdata['actionDate'] = date('d-m-Y');
  //   $emailDdata['actionTime'] = $currenttime;
  //   $emailDdata['total_success'] = $total_success_generate;
  //   $emailDdata['total_fail'] = $total_fail_generate;
  //   $emailDdata['totalRecords'] = count($result_data);
  //   EmailHelper::sendEmailDirect($emailDdata);

  //   $emailDdata['emailTo']= 'allan.alzula@gmail.com';
  //   return EmailHelper::sendEmailDirect($emailDdata);
	  // return $result_data;
	}

	public function testNewPDF( ){
		$pdf = PDF::loadView('pdf-download/admin-transactions-company-invoice');
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

		return $pdf->download('statement.pdf');
	}

	public function showNewPDF( ){
		return View::make('pdf-download/admin-transactions-company-invoice');
	}
	
	public function spendingInvoiceHistoryList ( ) {

		$input = Input::all();
		$session = self::checkSession();
		$paginate = [];
		$limit = !empty($input['per_page']) ? $input['per_page'] : 10;
		$customer_id = $result->customer_buy_start_id;

		$customer_plans = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->get();

		$new_data = [];

		foreach ($customer_plans as $key => $cplan) {
			$active_plans = DB::table('customer_active_plan')->where('plan_id', $cplan->customer_plan_id)->get();
			foreach ($active_plans as $key => $plan) {
				if($plan->account_type == "stand_alone_plan" || $plan->account_type == "lite_plan" || $plan->account_type == "enterprise_plan") {
					$withdraws = DB::table('payment_refund')
					->where('customer_active_plan_id', $plan->customer_active_plan_id)
					->get();

					foreach ($withdraws as $key => $withdraw) {
						$refunds = DB::table('customer_plan_withdraw')
						->where('payment_refund_id', $withdraw->payment_refund_id)
						->whereIn('refund_status', [0, 1])
						->count('user_id');

						$amount = DB::table('customer_plan_withdraw')
						->where('payment_refund_id', $withdraw->payment_refund_id)
						->whereIn('refund_status', [0, 1])
						->sum('amount');

						if($amount > 0) {
							$temp = array(
								'customer_active_plan_id' => $withdraw->customer_active_plan_id,
								'payment_refund_id'		  => $withdraw->payment_refund_id,
								'total_amount'	=> number_format($amount, 2),
								'total_employees' => $refunds,
								'date_withdraw'	 => $withdraw->date_refund,
								'refund_data'		=> $withdraw,
								'currency_type' => $withdraw->currency_type
							);

							array_push($new_data, $temp);
						}

					}
				}
			}
		}

		$credits_statements_data = DB::table('company_credits_statement')
                                ->where('statement_customer_id', $customer_id)
                                ->get();

        $credits_statements = DB::table('company_credits_statement')
                                ->where('statement_customer_id', $customer_id)
								->paginate($limit);
								
		$deposits = DB::table("spending_deposit_credits")
		->where("customer_id", $session->customer_buy_start_id)
		->paginate($limit);
	}

	public function getListCompanyPlanWithdrawal( )
	{
		$input = Input::all();
		$limit = $input['limit'] ?? 5;

		if(empty($input['customer_active_plan_id']) || $input['customer_active_plan_id'] == null) {
			return ['status' => false, 'messsage' => 'customer_active_plan_id is required'];
		}

		$refunds = DB::table('payment_refund')
					->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', '=', 'payment_refund.customer_active_plan_id')
					->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_active_plan.customer_start_buy_id')
					->where('payment_refund.customer_active_plan_id', $input['customer_active_plan_id'])
					->orderBy('payment_refund.created_at', 'desc')
					->paginate($limit);
		
		$pagination = [];
		$pagination['last_page'] = $refunds->getLastPage();
		$pagination['current_page'] = $refunds->getCurrentPage();
		$pagination['total_data'] = $refunds->getTotal();
		$pagination['from'] = $refunds->getFrom();
		$pagination['to'] = $refunds->getTo();
		$pagination['count'] = $refunds->count();
		$format = [];

		foreach ($refunds as $key => $refund) {
			$result = \PlanHelper::getRefundLists($refund->payment_refund_id);
			$result['invoice_id'] = $refund->payment_refund_id;
			$result['payment_refund_id'] = $refund->payment_refund_id;
			$result['customer_buy_start_id'] = $refund->customer_buy_start_id;
			$result['customer_id'] = $refund->customer_buy_start_id;
			array_push($format, $result);
		}
		
		$pagination['data'] = $format;
		return $pagination;
	}
}
