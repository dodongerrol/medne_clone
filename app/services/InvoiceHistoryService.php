<?php

class InvoiceHistoryService
{
    protected $options = [
        'type' => null,
        'customer_id' => null,
        'per_page' => 20
    ];

    public function __construct(array $options)
    {
        $this->options = $options;
    }   

    public function getInvoiceHistory()
    {
        switch ($this->options['type']) {
            case 'plan':
                return $this->getPlanInvoiceHistory();
                break;
            
            default:
                // Silence is golden
                break;
        }
    }

    protected function getPlanInvoiceHistory()
    {
        $pagination = [];

        // $invoices = DB::table('customer_active_plan')
        // ->join('corporate_invoice', 'corporate_invoice.customer_active_plan_id', '=', 'customer_active_plan.customer_active_plan_id')
        // ->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_active_plan.customer_start_buy_id')
        // ->join('customer_link_customer_buy', 'customer_link_customer_buy.customer_buy_start_id', '=', 'customer_buy_start.customer_buy_start_id')
        // ->join('corporate', 'corporate.corporate_id', '=', 'customer_link_customer_buy.corporate_id')
        // ->where('customer_buy_start.customer_buy_start_id', $this->options['customer_id'])
        // ->orderBy('corporate_invoice.invoice_date', 'desc')
        // ->paginate($this->options['per_page']);
        $invoices = DB::table('corporate_invoice')
                        ->where('corporate_invoice.customer_id', $this->options['customer_id'])
                        ->orderBy('corporate_invoice.invoice_date', 'desc')
                        ->paginate($this->options['per_page']);

        $plan = DB::table('customer_plan')
            ->where('customer_buy_start_id', $this->options['customer_id'])
            ->orderBy('created_at', 'desc')
            ->first();


        $pagination['current_page'] = $invoices->getCurrentPage();
        $pagination['last_page'] = $invoices->getLastPage();
        $pagination['total'] = $invoices->getTotal();
        $pagination['per_page'] = $invoices->getPerPage();
        $pagination['count'] = $invoices->count();

        // foreach($invoices as $invoice) {
        //     $total = 0;
            
        //     $active = DB::table('customer_active_plan')
        //         ->where('customer_active_plan_id', $invoice->customer_active_plan_id)
        //         ->first();

        //     $calculated_prices = 0;
        //     $plan_amount = 0;

        //     $plan = DB::table('customer_plan')
        //         ->where('customer_plan_id', $active->plan_id)
        //         ->orderBy('created_at', 'desc')
        //         ->first();

        //     if($active->new_head_count == 0) {
        //         if((int)$invoice->override_total_amount_status == 1) {
        //             $calculated_prices = $invoice->override_total_amount;
        //         } else {
        //             $calculated_prices = $invoice->individual_price;
        //         }
        //         $plan_amount = $calculated_prices * $invoice->employees;
        //     } else {
        //         $calculated_prices_end_date = $plan->plan_end;

        //         if((int)$invoice->override_total_amount_status == 1) {
        //             $calculated_prices = $invoice->override_total_amount;
        //         } else {
        //             $calculated_prices = \CustomerHelper::calculateInvoicePlanPrice($invoice->individual_price, $active->plan_start, $calculated_prices_end_date);
        //             $calculated_prices = \DecimalHelper::formatWithNoCommas($calculated_prices);
        //         }
        //         $plan_amount = $calculated_prices * $invoice->employees;
        //     }
            
        //     $total += $plan_amount;
                  
        //     $dependents = DB::table('dependent_plans')
        //         ->where('customer_active_plan_id', $invoice->customer_active_plan_id)
        //         ->get();
            
        //     foreach ($dependents as $dependent) {
        //         $invoice_dependent = DB::table('dependent_invoice')
        //             ->where('dependent_plan_id', $dependent->dependent_plan_id)
        //             ->first();
    
        //         if((int)$dependent->new_head_count == 1) {
        //             $calculated_prices_end_date = \CustomerHelper::getCompanyPlanDates($plan->customer_buy_start_id);
        //             $calculated_prices_end_date = date('Y-m-d', strtotime('+1 day', strtotime($calculated_prices_end_date['plan_end'])));
        //             $calculated_prices = \BenefitsPlanHelper::calculateInvoicePlanPrice($invoice_dependent->individual_price, $dependent->plan_start, $calculated_prices_end_date);
        //             $total += $calculated_prices * $dependent->total_dependents;
        //         } else {
        //             $total += $dependent->individual_price * $dependent->total_dependents;
        //         }
        //     }
            
        //     $payment_data = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
        //     $amount_due = $payment_data ? \DecimalHelper::formatWithNoCommas(round($total - $payment_data->paid_amount, 2)) : round($total, 2);
        //     $pagination['data'][] = [
        //         'id' => $invoice->corporate_invoice_id,
        //         'invoice_date' => date('Y-m-d', strtotime($invoice->invoice_date)),
        //         'payment_due' => date('Y-m-d', strtotime($invoice->invoice_due)),
        //         'number' => $invoice->invoice_number,
        //         // 'total'  => $total,
        //         'amount_due' => $amount_due <= 0 ? "0.00" : \DecimalHelper::formatDecimal($amount_due),
        //         'payment_amount'  => $payment_data ? \DecimalHelper::formatDecimal($payment_data->paid_amount) : "0.00",
        //         'paid_date'  => $payment_data && $active->paid == "true" ? date('Y-m-d', strtotime($payment_data->date_received)) : null,
        //         'type' => null,
        //         'payment_method' => null,
        //         'payment_remarks' => $payment_data ? $payment_data->remarks : null,
        //         'cheque_logs_id'  => $payment_data ? $payment_data->cheque_logs_id : null,
        //         'currency_type'   => $invoice->currency_type,
        //         'status'  => $amount_due <= 0 ? true : false,
        //         'category_type'	=> $this->options['type']
        //     ];
        // }

        foreach($invoices as $invoice) {
            $total = 0;
            $active = DB::table('customer_active_plan')
                ->where('customer_active_plan_id', $invoice->customer_active_plan_id)
                ->first();

            $calculated_prices = 0;
            $plan_amount = 0;

            $plan = DB::table('customer_plan')
                ->where('customer_plan_id', $active->plan_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if($active->new_head_count == 0) {
                if((int)$invoice->override_total_amount_status == 1) {
                    $calculated_prices = $invoice->override_total_amount;
                } else {
                    $calculated_prices = $invoice->individual_price;
                }
                $plan_amount = $calculated_prices * $invoice->employees;
            } else {
                // $calculated_prices_end_date = $plan->plan_end;
                $calculated_prices_end_date = \CustomerHelper::getCompanyPlanDates($active->customer_start_buy_id);
				$end_plan_date = $calculated_prices_end_date['plan_end'];
				$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
                if((int)$invoice->override_total_amount_status == 1) {
                    $calculated_prices = $invoice->override_total_amount;
                } else {
                    $calculated_prices = \CustomerHelper::calculateInvoicePlanPrice($invoice->individual_price, $active->plan_start, $calculated_prices_end_date);
                    $calculated_prices = \DecimalHelper::formatWithNoCommas($calculated_prices);
                }
                $plan_amount = $calculated_prices * $invoice->employees;
            }
            
            $total += $plan_amount;
                  
            $dependents = DB::table('dependent_plans')
                ->where('customer_active_plan_id', $invoice->customer_active_plan_id)
                ->get();
            
            foreach ($dependents as $dependent) {
                $with_dependent = true;
                $invoice_dependent = DB::table('dependent_invoice')
                    ->where('dependent_plan_id', $dependent->dependent_plan_id)
                    ->first();
    
                if((int)$dependent->new_head_count == 1) {
                    $calculated_prices_end_date = \CustomerHelper::getCompanyPlanDates($plan->customer_buy_start_id);
                    $calculated_prices_end_date = date('Y-m-d', strtotime('+1 day', strtotime($calculated_prices_end_date['plan_end'])));
                    $calculated_prices = \CustomerHelper::calculateInvoicePlanPrice($invoice_dependent->individual_price, $dependent->plan_start, $calculated_prices_end_date);
                    $total += $calculated_prices * $dependent->total_dependents;
                } else {
                    $total += $dependent->individual_price * $dependent->total_dependents;
                }
            }
            
            $payment_data = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();

            if ($payment_data) {
                $amount_due = $payment_data->paid_amount >= $total ? 0 : round($total - $payment_data->paid_amount, 2);
            } else {
                $amount_due = round($total, 2);
            }

            $pagination['data'][] = [
                'id' => $invoice->corporate_invoice_id,
                'invoice_date' => date('Y-m-d', strtotime($invoice->invoice_date)),
                'payment_due' => date('Y-m-d', strtotime($invoice->invoice_due)),
                'number' => $invoice->invoice_number,
                // 'total'  => $total,
                'amount_due' => $amount_due <= 0 ? "0.00" : \DecimalHelper::formatDecimal($amount_due),
                'payment_amount'  => $payment_data ? \DecimalHelper::formatDecimal($payment_data->paid_amount) : "0.00",
                'paid_date'  => $payment_data && $active->paid == "true" ? date('Y-m-d', strtotime($payment_data->date_received)) : null,
                'type' => null,
                'payment_method' => null,
                'payment_remarks' => $payment_data ? $payment_data->remarks : null,
                'cheque_logs_id'  => $payment_data ? $payment_data->cheque_logs_id : null,
                'currency_type'   => $invoice->currency_type,
                'status'  => $amount_due <= 0 ? true : false,
                'category_type'	=> $this->options['type']
            ];
        }

        return $pagination;
    }
}