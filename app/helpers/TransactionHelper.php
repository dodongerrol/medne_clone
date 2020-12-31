<?php
class TransactionHelper
{
	
	public static function getClinicImageType($clinic_type)
	{
		$type = "";
		$image = "";
		$clinic_type_name = "";

		if((int)$clinic_type->head == 1 || $clinic_type->head == "1") {
		if($clinic_type->Name == "GP") {
		$type = "general_practitioner";
		$clinic_type_name = "GP";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
		} else if($clinic_type->Name == "Dental") {
		$type = "dental_care";
		$clinic_type_name = "Dental";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
		} else if($clinic_type->Name == "TCM") {
		$type = "tcm";
		$clinic_type_name = "TCM";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
		} else if($clinic_type->Name == "Screening") {
		$type = "health_screening";
		$clinic_type_name = "Screening";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
		} else if($clinic_type->Name == "Wellness") {
		$type = "wellness";
		$clinic_type_name = "Wellness";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
		} else if($clinic_type->Name == "Specialist") {
		$type = "health_specialist";
		$clinic_type_name = "Specialist";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
		}
		} else {
		$find_head = DB::table('clinic_types')
		->where('ClinicTypeID', $clinic_type->sub_id)
		->first();
		if($find_head->Name == "GP") {
		$type = "general_practitioner";
		$clinic_type_name = "GP";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
		} else if($find_head->Name == "Dental") {
		$type = "dental_care";
		$clinic_type_name = "Dental";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
		} else if($find_head->Name == "TCM") {
		$type = "tcm";
		$clinic_type_name = "TCM";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
		} else if($find_head->Name == "Screening") {
		$type = "health_screening";
		$clinic_type_name = "Screening";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
		} else if($find_head->Name == "Wellness") {
		$type = "wellness";
		$clinic_type_name = "Wellness";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
		} else if($find_head->Name == "Specialist") {
		$type = "health_specialist";
		$clinic_type_name = "Specialist";
		$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
		}
		}

	    return array('type' => $type, 'image' => $image, 'clinic_type_name' => $clinic_type_name);
	}

	public static function getCoPayment($clinic, $date, $user_id)
	{
		$peak_amount = 0;
		$consultation_fees = 0;
    	$clinic_peak_status = false;
		$credits = DB::table('e_wallet')->where('UserID', $user_id)->first();

		if($credits->currency_type == "sgd") {
			return array('co_paid_amount' => 0, 'co_paid_status' => 0, 'peak_amount' => 0, 'consultation_fees' => 0, 'clinic_peak_status' => false);
		}

		// check clinic peak hours
		$result = ClinicHelper::getCheckClinicPeakHour($clinic, $date);
		if($result['status']) {
			$peak_amount = $result['amount'];
			$clinic_peak_status = true;
			// check user company peak status
			$user_peak = PlanHelper::getUserCompanyPeakStatus($user_id);
			if($user_peak) {
			if((int)$clinic->co_paid_status == 1) {
			$gst = $peak_amount * $clinic->gst_percent;
			$co_paid_amount = $peak_amount + $gst;
			$co_paid_status = $clinic->co_paid_status;
			} else {
			$co_paid_amount = $peak_amount;
			$co_paid_status = $clinic->co_paid_status;
			}

			if((int)$clinic->consultation_gst_status == 1) {
				$consult_gst = $peak_amount * $clinic->gst_percent;
				$consult_paid_amount = $peak_amount + $consult_gst;
				$consultation_fees = $consult_paid_amount;
			} else {
				$consultation_fees = $peak_amount;
			}
		} else {
			if((int)$clinic->co_paid_status == 1) {
			$gst = $peak_amount * $clinic->gst_percent;
			$co_paid_amount = $peak_amount + $gst;
			$co_paid_status = $clinic->co_paid_status;
		} else {
			$co_paid_amount = $peak_amount;
			$co_paid_status = $clinic->co_paid_status;
		}

		if((int)$clinic->consultation_gst_status == 1) {
			$consult_gst = $clinic->consultation_fees * $clinic->gst_percent;
			$consult_paid_amount = $clinic->consultation_fees + $consult_gst;
			$consultation_fees = $consult_paid_amount;
		} else {
			$consultation_fees = $clinic->consultation_fees;
		}
		}
		} else {
		if((int)$clinic->co_paid_status == 1) {
			$gst = $clinic->co_paid_amount * $clinic->gst_percent;
			$co_paid_amount = $clinic->co_paid_amount + $gst;
			$co_paid_status = $clinic->co_paid_status;
		} else {
			$co_paid_amount = $clinic->co_paid_amount;
			$co_paid_status = $clinic->co_paid_status;
		}

		if((int)$clinic->consultation_gst_status == 1) {
			$consult_gst = $clinic->consultation_fees * $clinic->gst_percent;
			$consult_paid_amount = $clinic->consultation_fees + $consult_gst;
			$consultation_fees = $consult_paid_amount;
		} else {
			$consultation_fees = $clinic->consultation_fees;
		}
		}

    	return array('co_paid_amount' => $co_paid_amount, 'co_paid_status' => $co_paid_status, 'peak_amount' => $peak_amount, 'consultation_fees' => (float)$consultation_fees, 'clinic_peak_status' => $clinic_peak_status);
	}


  public static function floatvalue($val){
    return str_replace(",", "", $val);
    $val = str_replace(",",".",$val);
    $val = preg_replace('/\.(?=.*\.)/', '', $val);
    return floatval($val);
  }

  public static function getInNetworkSpent($user_id, $spending_type)
  {
    $wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

    if($spending_type == 'medical') {
      $table_wallet_history = 'wallet_history';
      $history_column_id = "wallet_history_id";
    } else {
      $table_wallet_history = 'wellness_wallet_history';
      $history_column_id = "wellness_wallet_history_id";
    }

    $e_claim_spent = DB::table($table_wallet_history)
    ->where('wallet_id', $wallet->wallet_id)
    ->where('where_spend', 'e_claim_transaction')
    ->sum('credit');

    $in_network_temp_spent = DB::table($table_wallet_history)
    ->where('wallet_id', $wallet->wallet_id)
    ->where('where_spend', 'in_network_transaction')
    ->sum('credit');

    $credits_back = DB::table($table_wallet_history)
    ->where('wallet_id', $wallet->wallet_id)
    ->where('where_spend', 'credits_back_from_in_network')
    ->sum('credit');

    $in_network_spent = $in_network_temp_spent - $credits_back;
    $current_spending = $in_network_spent + $e_claim_spent;
    return array('in_network_spent' => $in_network_spent, 'e_claim_spent' => $e_claim_spent, 'current_spending' => $current_spending);
  }

  	public static function getTransactionDetails($transaction_id)
	{
		$consultation_cash = false;
		$consultation_credits = false;
		$service_cash = false;
		$service_credits = false;
		$consultation = 0;
		$trans = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();

		if($trans) {
			if($trans->spending_type == 'medical') {
				$table_wallet_history = 'wallet_history';
			} else {
				$table_wallet_history = 'wellness_wallet_history';
			}
			if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
				if($trans->lite_plan_enabled == 1) {
					$logs_lite_plan = DB::table($table_wallet_history)
					->where('logs', 'deducted_from_mobile_payment')
					->where('lite_plan_enabled', 1)
					->where('id', $trans->transaction_id)
					->first();

					if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
						$consultation_credits = true;
						$service_credits = true;
						$consultation = floatval($logs_lite_plan->credit);
					} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
						$consultation_credits = true;
						$service_credits = true;
						$consultation = floatval($logs_lite_plan->credit);
					} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
						$consultation = floatval($trans->consultation_fees);
					}
				}


				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";
				$procedure = "";

				// get services
				if((int)$trans->multiple_service_selection == 1)
				{
							// get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
								// $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

						// check if there is a receipt image
				$receipts = DB::table('user_image_receipt')
				->where('transaction_id', $trans->transaction_id)
				->get();

				$doc_files = [];
				$receipt_status = FALSE;
				if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
							// $receipt_status = TRUE;
					$health_provider_status = TRUE;
				} else {
					$health_provider_status = FALSE;
				}

				$type = self::getClinicImageType($clinic_type);
			// check user if it is spouse or dependent
				if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
					$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$dependent_relationship = FALSE;
					$owner_id = $customer->UserID;
				}

				$half_credits = false;
				$total_amount = $trans->procedure_cost;
				$procedure_cost = $trans->credit_cost;

				if((int)$trans->health_provider_done == 1) {
					$payment_type = "Cash";
					$transaction_type = "cash";
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->half_credits == 1) {
							$total_amount = $trans->credit_cost + $trans->consultation_fees;
							$cash = $trans->cash_cost;
						} else {
							$total_amount = $trans->procedure_cost;
							$total_amount = $trans->procedure_cost + $trans->consultation_fees;
							$cash = $trans->procedure_cost;
						}
					} else {
						if((int)$trans->half_credits == 1) {
							$cash = $trans->cash_cost;
						} else {
							$cash = $trans->procedure_cost;
						}
					}
				} else {
					if($trans->credit_cost > 0 && $trans->cash_cost > 0) {
						$payment_type = 'Mednefits Credits + Cash';
						$half_credits = true;
					} else {
						$payment_type = 'Mednefits Credits';
					}
					$transaction_type = "credits";
					// $cash = number_format($trans->credit_cost, 2);
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->half_credits == 1) {
							$total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
							$procedure_cost = $trans->credit_cost + $trans->consultation_fees;
							$transaction_type = "credit_cash";
				// $total_amount = $trans->credit_cost + $trans->cash_cost;
							$cash = $trans->cash_cost;
						} else {
							$total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
				// $total_amount = $trans->procedure_cost;
							if($trans->credit_cost > 0) {
								$cash = 0;
							} else {
								$cash = $trans->procedure_cost - $trans->consultation_fees;
							}
						}
					} else {
						$total_amount = $trans->procedure_cost;
						if((int)$trans->half_credits == 1) {
							$cash = $trans->cash_cost;
						} else {
							if($trans->credit_cost > 0) {
								$cash = 0;
							} else {
								$cash = $trans->procedure_cost;
							}
						}
					}
				}

				$bill_amount = 0;
				if((int)$trans->half_credits == 1) {
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->health_provider_done == 1) {
							$bill_amount = $trans->procedure_cost;
						} else {
							$bill_amount = $trans->credit_cost + $trans->cash_cost;
						}
					} else {
						$bill_amount = 	$trans->procedure_cost;
					}
				} else {
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->lite_plan_use_credits == 1) {
							$bill_amount = 	$trans->procedure_cost;
						} else {
							if((int)$trans->health_provider_done == 1) {
								$bill_amount = 	$trans->procedure_cost;
							} else {
								$bill_amount = 	$trans->credit_cost + $trans->cash_cost;
							}
						}
					} else {
						$bill_amount = 	$trans->procedure_cost;
					}
				}

				if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 0) {
					$procedure_cost = $trans->procedure_cost;
				} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
					$total_in_network_spent_credits_transaction = $trans->credit_cost;
				}

				$refund_text = 'NO';
				if((int)$trans->refunded == 1 && (int)$trans->deleted == 1) {
					$status_text = 'REFUNDED';
					$refund_text = 'YES';
				} else if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 1) {
					$status_text = 'REMOVED';
					$refund_text = 'YES';
				} else {
					$status_text = FALSE;
				}

				$paid_by_credits = $trans->credit_cost;
				if((int)$trans->lite_plan_enabled == 1) {
					if($consultation_credits == true) {
						$paid_by_credits += $consultation;
					}
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
				if($trans->currency_type == "myr" && $trans->default_currency == "sgd") {
					$total_amount = $total_amount * $trans->currency_amount;
					$trans->credit_cost = $trans->credit_cost * $trans->currency_amount;
					$trans->cap_per_visit = $trans->cap_per_visit * $trans->currency_amount;
					$trans->cash_cost = $trans->cash_cost * $trans->currency_amount;
					$consultation_credits = $consultation_credits * $trans->currency_amount;
					$paid_by_credits = $paid_by_credits * $trans->currency_amount;
					$trans->consultation_fees = $trans->consultation_fees * $trans->currency_amount;
					$trans->currency_type = "myr";
					$bill_amount = $bill_amount * $trans->currency_amount;
				} else  if($trans->default_currency == "sgd" || $trans->currency_type == "myr") {
					$trans->currency_type = "sgd";
        		}
        
				return array(
					'clinic_name'       => $clinic->Name,
					'health_provider_name'      => $clinic->image,
					'health_provider_address'	=> $clinic->Address,
					'health_provider_city'	=> $clinic->City,
					'health_provider_country'	=> $clinic->Country,
					'health_provider_phone'	=> $clinic->Phone,
					'health_provider_postal'	=> $clinic->Postal,
					'total_amount'            => number_format($total_amount, 2),
					'credits'    => number_format($total_amount, 2),
					'bill_amount'    => number_format($bill_amount, 2),
					'health_provider_name' => $clinic_name,
					'service'         => $procedure,
					'transaction_date' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
					'member'            => ucwords($customer->Name),
					'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'trans_id'          => $trans->transaction_id,
					'receipt_status'    => $receipt_status,
					'health_provider_status' => $health_provider_status,
					'user_id'           => $trans->UserID,
					'type'              => $payment_type,
					'month'             => date('M', strtotime($trans->created_at)),
					'day'               => date('d', strtotime($trans->created_at)),
					'time'              => date('h:ia', strtotime($trans->created_at)),
					'clinic_type'       => $type['clinic_type_name'],
					'owner_account'     => $sub_account,
					'owner_id'          => $owner_id,
					'sub_account_user_type' => $sub_account_type,
					'co_paid'           => $trans->consultation_fees,
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
					'refund_text'       => $refund_text,
					'cash'              => $cash,
					'status_text'       => $status_text,
					'spending_type'     => ucwords($trans->spending_type),
					'consultation'      => (int)$trans->lite_plan_enabled == 1 ?number_format($trans->consultation_fees, 2) : "0.00",
					'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
					'consultation_credits' => $consultation_credits,
					'service_credits'   => $service_credits,
					'transaction_type'  => $transaction_type,
					'logs_lite_plan'    => isset($logs_lite_plan) ? $logs_lite_plan : null,
					'dependent_relationship'    => $dependent_relationship,
					'cap_transaction'   => $half_credits,
					'cap_per_visit'     => $trans->cap_per_visit > 0 ? number_format($trans->cap_per_visit, 2) : 'Not Applicable',
					'cap_per_visit_status' => $trans->cap_per_visit > 0 ? true : false,
					'paid_by_cash'      => number_format($trans->cash_cost, 2),
					'paid_by_credits'   => number_format($paid_by_credits, 2),
					"currency_symbol" 	=> $trans->currency_type == "myr" ? "MYR" : "SGD",
					"currency_type" 	=> $trans->currency_type == "myr" ? "MYR" : "SGD",
					'files'				=> $doc_files,
					'visit_deduction'	=> (int)$clinic_type->visit_deduction == 1 ? true : false
				);
			}
		}
	}

	public static function insertTransactionToCompanyInvoice($transaction_id, $member_id, $plan_method)
	{
		$transaction = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();

		if($transaction) {
			$member_id = StringHelper::getUserId($member_id);
			$customer_id = PlanHelper::getCustomerId($member_id);

			if($customer_id) {
				$start = date('Y-m-01', strtotime($transaction->created_at));
				$end = date('Y-m-t', strtotime($transaction->created_at));
				$statement = DB::table('company_credits_statement')
						->where('statement_customer_id', $customer_id)
						->where('statement_start_date', $start)
						->where('plan_method', $plan_method)
						->first();

				if($statement) {
					$data = array(
						'invoice_id' => $statement->statement_id,
						'transaction_id'	=> $transaction_id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
					);

					DB::table('spending_invoice_transactions')->insert($data);
				} else {
					// create new spending invoice
					SpendingInvoiceLibrary::createStatement($customer_id, $start, PlanHelper::endDate($end), $plan_method);
				}
			}
		}
	}
}
?>