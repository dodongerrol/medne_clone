<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class Transaction extends Eloquent {

	protected $table = 'transaction_history';
    protected $guarded = [];
    // protected $dates = ['created_at', 'updated_at', 'date_of_transaction']
    // public $timestamps = false;

    function createTransaction($data)
    {
        // $check_duplicate = Transaction::where('UserID', $data['UserID'])
        //                             ->where('ProcedureID', $data['ProcedureID'])
        //                             ->where('date_of_transaction', $data['date_of_transaction'])
        //                             ->where('ClinicID', $data['ClinicID'])
        //                             ->where('procedure_cost', $data['procedure_cost'])
        //                             ->where('AppointmenID', $data['AppointmenID'])
        //                             ->where('medi_percent', $data['medi_percent'])
        //                             ->where('clinic_discount', $data['clinic_discount'])
        //                             ->where('paid', $data['paid'])
        //                             ->where('credit_cost', $data['credit_cost'])
        //                             ->where('co_paid_status', $data['co_paid_status'])
        //                             ->where('co_paid_amount', $data['co_paid_amount'])
        //                             ->where('DoctorID', $data['DoctorID'])
        //                             ->where('backdate_claim', $data['backdate_claim'])
        //                             ->where('in_network', $data['in_network'])
        //                             ->where('mobile', $data['mobile'])
        //                             ->where('health_provider_done', $data['health_provider_done'])
        //                             ->where('multiple_service_selection', $data['multiple_service_selection'])
        //                             ->count();
        // if($check_duplicate > 1) {
        //     return FALSE;
        // } else {
            return Transaction::create($data);
        // }
    }

    public function updateTransaction($id, $data)
    {
        return Transaction::where('transaction_id', $id)->update($data);
    }

    function getMinimumDate( )
    {
        $date = Transaction::min('updated_at');
        return date('Y-m-d', strtotime($date));
    }

    public function CalculateCoPaidTransaction($id, $amount_pay)
    {
        $wallet = new Wallet( );
        $procedure_data = new ClinicProcedures();
        $user = new User();
        $appointment_data = new UserAppoinment();

        $result = Transaction::where('transaction_id', '=', $id)->first();
        $credit = $wallet->getWalletAmount($result->wallet_id);
        $procedure = $procedure_data::where('ProcedureID', '=', $result->ProcedureID)->first();

        $client = $user::where('UserID', '=', $result->UserID)->first();
        $appointment = $appointment_data::where('UserAppoinmentID', '=', $result->AppointmenID)->first();



        // if($amount_pay <= 500 ) {
        //     if(strrpos($result->clinic_discount, '%')) {
        //         $percentage = chop($result->clinic_discount, '%');
        //         $discount = (int)$percentage / 100 + $result->medi_percent;
        //         // return $discount;
        //         // return 'use percentage';
        //     } else {
        //         // return 'use whole number';
        //         $discount_clinic = str_replace('$', '', $result->clinic_discount);
        //         $discount = $discount_clinic;
        //     }

        //     if($result->co_paid_status == 1) {
        //         $sub = $discount + $result->co_paid_amount;
        //         $total_amount = $amount_pay - $sub;
        //         $medi = '$'.$result->co_paid_amount;
        //     } else {
        //         $sub = $amount_pay * $discount;
        //         $total_amount = $amount_pay - $sub;
        //         $medi = $result->medi_percent * 100 .'%';
        //     }
            $total_amount = $amount_pay;
            if((int)$credit->balance >= $total_amount) {
                $deducted = round($credit->balance, 2) - round($total_amount, 2);
                $deducted_credit = round($credit->balance, 2) - round($deducted, 2);
                $final_bill = 0;
                // return "hello";
            } else {
                // return round($total_amount, 2);
                $deducted = round($total_amount, 2) - round($credit->balance, 2);
                // return $deducted;
                $final_bill = round($deducted, 2);
                // return $final_bill;
                if($credit->balance == 0) {
                    $deducted_credit = 0;
                } else {
                    $deducted_credit = round($credit->balance, 2);
                }
            }
        // } else {
        //     // $total_amount = $amount_pay;
        //     if(strrpos($result->clinic_discount, '%')) {
        //         $percentage = chop($result->clinic_discount, '%');
        //         $discount = (int)$percentage / 100;
        //         // return $discount;
        //         // return 'use percentage';
        //     } else {
        //         // return 'use whole number';
        //         $discount_clinic = str_replace('$', '', $result->clinic_discount);
        //         $discount = round((int)$discount_clinic/100, 1);
        //     }
        //     $sub = $amount_pay * $discount;
        //     $total_amount = $amount_pay - $sub;

        //     if((int)$credit->balance > $total_amount) {
        //         $deducted_credit = $credit->balance - round($total_amount, 2);
        //         $final_bill = 0;
        //     } else {
        //         $deducted_credit = round($total_amount, 2) - $credit->balance;
        //         $final_bill = $deducted_credit;
        //     }
        //     $medi = "0%";
        // }
        // return $final_bill;



        $summary = array(
            'name'          => $client->Name,
            'nric'          => $client->NRIC,
            'procedure'     => $procedure->Name,
            'date'          => date("F j, Y", $appointment->BookDate),
            'time'          => date("g:i a", $appointment->StartTime)." - ".date("g:i a", $appointment->EndTime),
            'email'         => $client->Email,
            'total_amount'  => $amount_pay,
            'medi_credit'   => $deducted_credit,
            'total_bill'    => $final_bill,
            // 'total_without_percentage' => $total_amount,
            // 'clinic_discount' => $result->clinic_discount,
            // 'medi_percent'  => $medi,
            'transaction_id' => $id,
            'user_id'       => $result->UserID,
            'credit'        => $credit->balance,
            'wallet_id'     => $credit->wallet_id,
            'appointment_id' => $result->AppointmenID,
            'procedureid'   => $result->ProcedureID,
            'doctorid'      => $result->DoctorID
        );
         $gst = DB::table('clinic')->where('ClinicID', $result->ClinicID)->select('gst_amount')->first();
        // return $gst;
        Transaction::where('transaction_id', '=', $result->transaction_id)
                    ->update([
                        'current_wallet_amount'     => $credit->balance,
                        'credit_cost'               => $deducted_credit,
                        'procedure_cost'            => $amount_pay,
                        'co_paid_amount'            => $gst->gst_amount
                    ]);

        return $summary;
    }

    public function newCalculateTransaction($id, $amount_pay)
    {
        $wallet = new Wallet( );
        $procedure_data = new ClinicProcedures();
        $user = new User();
        $appointment_data = new UserAppoinment();

        $result = Transaction::where('transaction_id', '=', $id)->first();
        $credit = $wallet->getWalletAmount($result->wallet_id);
        $procedure = $procedure_data::where('ProcedureID', '=', $result->ProcedureID)->first();

        $client = $user::where('UserID', '=', $result->UserID)->first();
        $appointment = $appointment_data::where('UserAppoinmentID', '=', $result->AppointmenID)->first();



        if($amount_pay <= 500 ) {
            if(strrpos($result->clinic_discount, '%')) {
                $percentage = chop($result->clinic_discount, '%');
                $discount = (int)$percentage / 100 + $result->medi_percent;
                // return $discount;
                // return 'use percentage';
            } else {
                // return 'use whole number';
                $discount_clinic = str_replace('$', '', $result->clinic_discount);
                $discount = $discount_clinic;
            }

            if($result->co_paid_status == 1) {
                $sub = $discount + $result->co_paid_amount;
                $total_amount = $amount_pay - $sub;
                $medi = '$'.$result->co_paid_amount;
            } else {
                $sub = $amount_pay * $discount;
                $total_amount = $amount_pay - $sub;
                $medi = $result->medi_percent * 100 .'%';
            }

            if((int)$credit->balance >= $total_amount) {
                $deducted = round($credit->balance, 2) - round($total_amount, 2);
                $deducted_credit = round($credit->balance, 2) - round($deducted, 2);
                $final_bill = 0;
                // return "hello";
            } else {
                // return round($total_amount, 2);
                $deducted = round($total_amount, 2) - round($credit->balance, 2);
                // return $deducted;
                $final_bill = round($deducted, 2);
                // return $final_bill;
                if($credit->balance == 0) {
                    $deducted_credit = 0;
                } else {
                    $deducted_credit = round($credit->balance, 2);
                }
            }
        } else {
            // $total_amount = $amount_pay;
            if(strrpos($result->clinic_discount, '%')) {
                $percentage = chop($result->clinic_discount, '%');
                $discount = (int)$percentage / 100;
                // return $discount;
                // return 'use percentage';
            } else {
                // return 'use whole number';
                $discount_clinic = str_replace('$', '', $result->clinic_discount);
                $discount = round((int)$discount_clinic/100, 1);
            }
            $sub = $amount_pay * $discount;
            $total_amount = $amount_pay - $sub;

            if((int)$credit->balance > $total_amount) {
                $deducted_credit = $credit->balance - round($total_amount, 2);
                $final_bill = 0;
            } else {
                $deducted_credit = round($total_amount, 2) - $credit->balance;
                $final_bill = $deducted_credit;
            }
            $medi = "0%";
        }
        // return $final_bill;
        $summary = array(
            'name'          => $client->Name,
            'nric'          => $client->NRIC,
            'procedure'     => $procedure->Name,
            'date'          => date("F j, Y", $appointment->BookDate),
            'time'          => date("g:i a", $appointment->StartTime)." - ".date("g:i a", $appointment->EndTime),
            'email'         => $client->Email,
            'total_amount'  => $amount_pay,
            'medi_credit'   => $deducted_credit,
            'total_bill'    => $final_bill,
            'total_without_percentage' => $total_amount,
            'clinic_discount' => $result->clinic_discount,
            'medi_percent'  => $medi,
            'transaction_id' => $id,
            'user_id'       => $result->UserID,
            'credit'        => $credit->balance,
            'wallet_id'     => $credit->wallet_id,
            'appointment_id' => $result->AppointmenID,
            'procedureid'   => $result->ProcedureID,
            'doctorid'      => $result->DoctorID,
            'status'        => true
        );

        Transaction::where('transaction_id', '=', $result->transaction_id)
                    ->update([
                        'current_wallet_amount'     => $credit->balance,
                        'credit_cost'               => $deducted_credit,
                        'procedure_cost'            => $amount_pay,
                    ]);

        return $summary;
    }

    function calculateTransaction($id, $amount_pay, $wallet_status)
    {
        $result = Transaction::where('transaction_id', '=', $id)->first();
        $sub_total = $amount_pay * $result->medi_percent;
        $wallet = new Wallet( );
        $procedure_data = new ClinicProcedures();
        $credit = $wallet->getWalletAmount($result->wallet_id);
        $procedure = $procedure_data::where('ProcedureID', '=', $result->ProcedureID)->first();
        $diff = $credit->balance - $sub_total;

        if($credit->balance >= $amount_pay) {
            // $credit_used - $sub_total;
            $balance = $credit->balance - $amount_pay; // 200 - 150 = 50 ( credit balance )
            $credit_used = $credit->balance -$balance; // 200 - 50 = 150 ( credit used )
            $revenue  = $credit_used - $sub_total; // 150 - 15 = 135 ( clinic's revenue )
            $debit = $revenue;
            $final_bill = 0;
            $total_revenue = $revenue;

        } elseif($credit->balance < $amount_pay) {
            $final_bill = $amount_pay - $credit->balance; // 150 - 5 = 145
            $credit_used = $amount_pay - $final_bill; // 150 - 145 = 5
            $balance = $credit->balance - $credit_used; // 5 - 5 = 0
            $revenue = $credit->balance - $sub_total; // 5 - 15 = -10
            $debit = $revenue;
            $total_revenue = $revenue + $final_bill;
        }
        else if($credit->balance <= $amount_pay) {
            $final_bill = $amount_pay - $credit->balance; // 150 - 5 = 145
            $credit_used = $final_bill - $amount_pay; // 150 - 145 = 5
            $balance = $credit->balance - $credit_used; // 5 - 5 = 0
            $revenue = $credit->balance - $sub_total; // 5 - 15 = -10
            $debit = $revenue;
            $total_revenue = $revenue + $final_bill;
        }

        // $current_user_balance = $credit->balance -

        // return $debit;
        // return $sub_total;
        if($debit > 0)
        {
            $state = 'medicloud pays clinic';
        } elseif($debit <= 0)
        {
            $state = 'clinic pays medicloud';
        }

        if($wallet_status == 1)
        {

            if($credit == "0")
            {
                return array('message' => 'user does not have enough credits.', 'status' => '450');
            } else {

                $credit_minus_TF = $credit->balance - $sub_total;
                $total = array(
                    'final_bill'        => $final_bill,
                    'credit_used'       => $credit_used,
                    'balance'           => $balance,
                    'debit'             => $debit,
                    'total_revenue'     => $total_revenue,
                    'status'            => $state,
                    'transaction_fee'   => $sub_total,

                );
            }

            if((int)$credit->balance >= (int)$result->procedure_cost) {
                $deducted = (int)$credit->balance - (int)$result->procedure_cost;
                $final_bill_result = "$0";
            } elseif((int)$credit->balance <= (int)$result->procedure_cost){
                $deducted = (int)$credit->balance - (int)$result->procedure_cost;
                $final_bill_result = "$0";
            } else {
                $deducted = (int)$credit->balance - (int)$result->procedure_cost;
                $final_bill_result = "$".$amount_pay;
            }
        } else {

            $total = array(
                'final_bill'        => $final_bill,
                'credit_used'       => $credit_used,
                'balance'           => $balance,
                'debit'             => $debit,
                'total_revenue'     => $total_revenue,
                'status'            => $state,
                'transaction_fee'   => $sub_total,
            );

            $deducted = "0";
            $final_bill_result = "$".$amount_pay;
        }

        $user = new User();
        $appointment_data = new UserAppoinment();
        $client = $user::where('UserID', '=', $result->UserID)->first();
        $appointment = $appointment_data::where('UserAppoinmentID', '=', $result->AppointmenID)->first();


        $summary = array(
            'name'          => $client->Name,
            'nric'          => $client->NRIC,
            'procedure'     => $procedure->Name,
            'date'          => date("F j, Y", $appointment->BookDate),
            'time'          => date("g:i a", $appointment->StartTime)." - ".date("g:i a", $appointment->EndTime),
            'total_amount'  => "$".$amount_pay,
            'deducted'      => $total['credit_used'],
            'email'         => $client->Email
        );

        Transaction::where('transaction_id', '=', $result->transaction_id)
                    ->update([
                        'revenue'                   => $total['total_revenue'],
                        'debit'                     => $total['debit'],
                        'current_wallet_amount'     => $credit['balance'],
                        'credit_cost'               => $total['credit_used'],
                        'procedure_cost'            => $amount_pay
                    ]);

        return array('summary' => $summary, 'total' => $total, 'transaction' => $result, 'wallet_use' => $wallet_status, 'final_bill' => $final_bill);
    }

    function getTransaction($appointment_id)
    {
        // return $appointment_id;
        $result = DB::table('transaction_history')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->join('e_wallet', 'e_wallet.wallet_id', '=', 'transaction_history.wallet_id')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID' ,'=', 'transaction_history.ProcedureID')
                ->where('transaction_history.AppointmenID', '=', $appointment_id)
                ->get();
        return $result;

    }

    function getTransactionDetails($id)
    {
        return Transaction::where('transaction_id', '=', $id)->first();
    }

    function finishTransaction($data, $id)
    {
        return Transaction::where('transaction_id', '=', $id)->update($data);
    }

    function getClinicTotalRevenue($id, $start, $end)
    {
        $credit = 0;
        if(strlen($start) > 0 && strlen($end)) {
            $collected_one = Transaction::where('ClinicID', '=', $id)
                                    ->where('credit_cost', '=', 0)
                                    ->where('paid', '=', 1)
                                    ->where('paid_medi', '=', 0)
                                    ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                                    ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                                    ->sum('procedure_cost');
            $collected_two = Transaction::where('ClinicID', '=', $id)
                                    ->where('credit_cost', '>', 0)
                                    ->where('paid', '=', 1)
                                    ->where('paid_medi', '=', 0)
                                    ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                                    ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                                    ->sum('procedure_cost');
            $medi_credit = Transaction::where('ClinicID', '=', $id)
                            ->where('credit_cost', '>', 0)
                            ->where('paid', '=', 1)
                            ->where('paid_medi', '=', 0)
                            ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                                    ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                            ->sum('credit_cost');
        } else {
            $collected_one = Transaction::where('ClinicID', '=', $id)
                                    ->where('credit_cost', '=', 0)
                                    ->where('paid', '=', 1)
                                    ->where('paid_medi', '=', 0)
                                    ->sum('procedure_cost');
            $collected_two = Transaction::where('ClinicID', '=', $id)
                                    ->where('credit_cost', '>', 0)
                                    ->where('paid', '=', 1)
                                    ->where('paid_medi', '=', 0)
                                    ->sum('procedure_cost');
        }
        return round($collected_one + $collected_two, 2);
    }

    function getClinicCredits($id, $start, $end)
    {
        if(strlen($start) > 0 && strlen($end)) {
            return Transaction::where('ClinicID', '=', $id)
                            ->where('credit_cost', '>', 0)
                            ->where('paid', '=', 1)
                            ->where('paid_medi', '=', 0)
                            ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                            ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                            ->sum('credit_cost');
        } else {
            return Transaction::where('ClinicID', '=', $id)
                            // ->where('credit_cost', '>', 0)
                            ->where('paid', '=', 1)
                            ->where('paid_medi', '=', 0)
                            ->sum('credit_cost');
        }
    }

    function getClinicCollected($id, $start, $end)
    {
        $credit = 0;
        if(strlen($start) > 0 && strlen($end)) {
            // $collected_one = Transaction::where('ClinicID', '=', $id)
            //                         ->where('credit_cost', '=', 0)
            //                         ->where('paid', '=', 1)
            //                         ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
            //                         ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
            //                         ->sum('revenue');
            // $collected_two = Transaction::where('ClinicID', '=', $id)
            //                         ->where('credit_cost', '>', 0)
            //                         ->where('paid', '=', 1)
            //                         ->where('paid_medi', '=', 0)
            //                         ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
            //                         ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
            //                         ->get();
            // $medi_credit = Transaction::where('ClinicID', '=', $id)
            //                 ->where('credit_cost', '>', 0)
            //                 ->where('paid', '=', 1)
            //                 ->where('paid_medi', '=', 0)
            //                 ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
            //                 ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
            //                 ->sum('credit_cost');
            // $total_revenue = Transaction::where('ClinicID', '=', $id)
            //                 ->where('paid', '=', 1)
            //                 ->where('paid_medi', '=', 0)
            //                 ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
            //                 ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
            //                 ->sum('procedure_cost');
            $collected = Transaction::where('ClinicID', '=', $id)
                        ->where('paid', '=', 1)
                        ->where('paid_medi', '=', 0)
                        ->where('updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                        ->where('updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                        ->get();
        } else {
            // $collected_one = Transaction::where('ClinicID', '=', $id)
            //                         ->where('credit_cost', '=', 0)
            //                         ->where('paid', '=', 1)
            //                         ->sum('revenue');
            // $collected_two = Transaction::where('ClinicID', '=', $id)
            //                         ->where('credit_cost', '>', 0)
            //                         ->where('paid', '=', 1)
            //                         ->where('paid_medi', '=', 0)
            //                         ->get();
            // $medi_credit = Transaction::where('ClinicID', '=', $id)
            //                 ->where('credit_cost', '>', 0)
            //                 ->where('paid', '=', 1)
            //                 ->where('paid_medi', '=', 0)
            //                 ->sum('credit_cost');
            // $total_revenue = Transaction::where('ClinicID', '=', $id)
            //                 ->where('paid', '=', 1)
            //                 ->where('paid_medi', '=', 0)
            //                 ->sum('procedure_cost');
            $collected = Transaction::where('ClinicID', '=', $id)
                            ->where('paid', '=', 1)
                            ->where('paid_medi', '=', 0)
                            // ->limit(5)
                            ->orderBy('transaction_id', 'desc')
                            ->get();
        }
        $clinic = DB::table('clinic')->where('ClinicID', $id)->first();

        $temp = array();
        if($clinic->co_paid_status == 1) {
            foreach ($collected as $key => $value) {
                $credit += $value->co_paid_amount;
            }
        } else {
            foreach ($collected as $key => $value) {
                if(strrpos($value->clinic_discount, '%')) {
                    $percentage = chop($value->clinic_discount, '%');
                    $discount = $percentage / 100 + $value->medi_percent / 100;
                } else {
                    $discount_clinic = str_replace('$', '', $value->clinic_discount);
                    $discount = round($discount_clinic / 100, 1) + $value->medi_percent;
                }
                // $credit += (int)$value->procedure_cost - (int)$value->credit_cost;
                $sub = $value->procedure_cost * $discount;
                $credit += $sub + $value->credit_cost;
            }
        }
        return round($credit, 2);
    }

    function viewTransactionHistoryLimitView($id)
    {
        // return $id;
        return DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->where('transaction_history.ClinicID', '=', $id)
                ->where('transaction_history.paid', '=', 1)
                ->where('transaction_history.paid_medi', '=', 0)
                ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.date_of_transaction')
                // ->take(7)
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.date_of_transaction', 'asc')
                ->get();
    }

    function viewTransactionBulkHistoryLimitView($id)
    {
        // $transaction = [];
        return DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->where('transaction_history.ClinicID', '=', $id)
                ->where('transaction_history.paid', '=', 1)
                ->where('transaction_history.paid_medi', '=', 0)
                ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.clinic_discount')
                // ->take(7)
                // ->groupBy('transaction_history.transaction_id')
                ->where('transaction_history.backdate_claim', "true")
                ->orderBy('transaction_history.updated_at', 'asc')
                ->get();
        // $result = DB::table('bulk_transaction')
        //         ->join('user', 'user.UserID', '=', 'bulk_transaction.user_id')
        //         ->where('clinic_id', $id)
        //         ->select('user.Name', 'bulk_transaction.amount', 'bulk_transaction.updated_at', 'bulk_transaction.book_date')
        //         ->orderBy('bulk_transaction.updated_at', 'asc')
        //         ->get();
        // foreach ($result as $key => $value) {
        //     $temp = array(
        //         'Name'              => $value->Name,
        //         'procedure_cost'    => $value->amount,
        //         'updated_at'        => $value->updated_at,
        //         'BookDate'          => $value->book_date,
        //         'credit_cost'       => 0
        //     );
        //     array_push($transaction, $temp);
        // }

        return $transaction;
    }

    function viewTransactionByDate($start, $end, $id)
    {
        // return date("Y-m-d H:i:s", strtotime($start));
        return DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->where('transaction_history.ClinicID', '=', $id)
                ->where('transaction_history.paid', '=', 1)
                ->where('transaction_history.paid_medi', '=', 0)
                ->where('transaction_history.date_of_transaction', '>=', date("Y-m-d H:i:s", strtotime($start)))
                ->where('transaction_history.date_of_transaction', '<=', date("Y-m-d H:i:s", strtotime($end)))
                ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.date_of_transaction')
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.date_of_transaction', 'asc')
                ->get();
    }

    function viewTransactionBulkByDate($start, $end, $id)
    {
        $transaction = [];
        $result = DB::table('bulk_transaction')
                ->join('user', 'user.UserID', '=', 'bulk_transaction.user_id')
                ->where('clinic_id', $id)
                ->where('bulk_transaction.book_date', '>=', date("Y-m-d", strtotime($start)))
                ->where('bulk_transaction.book_date', '<=', date("Y-m-d", strtotime($end)))
                ->select('user.Name', 'bulk_transaction.amount', 'bulk_transaction.updated_at', 'bulk_transaction.book_date')
                ->orderBy('bulk_transaction.updated_at', 'asc')
                ->get();
        foreach ($result as $key => $value) {
            $temp = array(
                'Name'              => $value->Name,
                'procedure_cost'    => $value->amount,
                'updated_at'        => $value->updated_at,
                'BookDate'          => $value->book_date,
                'credit_cost'       => 0
            );
            array_push($transaction, $temp);
        }

        return $transaction;
    }

    function paymentAdminTransactionHistory($start, $end, $search)
    {

        // return strlen($search);
        if(strlen($search) > 0) {
            // return "using search";
            return DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
                ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.clinicID')
                ->where('transaction_history.paid', '=', 1)
                ->where('user.Name', 'like', '%'.$search.'%')
                ->orWhere('clinic.Name', 'like', '%'.$search.'%')
                ->orWhere('doctor.Name', 'like', '%'.$search.'%')
                ->orWhere('clinic_procedure.Name', 'like', '%'.$search.'%')
                ->select('clinic.Name as clinic_name', 'user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.clinic_discount')
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.updated_at', 'asc')
                ->get();
        } else {
            // return "using date";
            return DB::table('transaction_history')
                    ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                    ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                    ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                    ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                    ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
                    ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.clinicID')
                    ->where('transaction_history.paid', '=', 1)
                    ->where('transaction_history.updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                    ->where('transaction_history.updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                    ->select('clinic.Name as clinic_name', 'user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.clinic_discount')
                    ->groupBy('transaction_history.transaction_id')
                    ->orderBy('transaction_history.updated_at', 'asc')
                    ->get();

            }
    }

    function paymentAdminViewTransactionHistory($start, $end, $filter, $clinicID)
    {


        if($filter == 0 || $filter == 1) {
            return DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
                ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.clinicID')
                ->where('transaction_history.paid', '=', 1)
                ->where('clinic.clinicID', '=', $clinicID)
                ->where('transaction_history.updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                ->where('transaction_history.updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                ->select('clinic.Name as clinic_name', 'user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.transaction_id', 'transaction_history.clinic_discount')
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.updated_at', 'asc')
                ->get();
        // } else if($filter == 1) {
            // $array = array();
            // $data = DB::table('transaction_history')
            //     ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
            //     ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
            //     ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
            //     ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
            //     ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
            //     ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.clinicID')
            //     ->where('transaction_history.paid', '=', 1)
            //     ->where('clinic.clinicID', '=', $clinicID)
            //     // ->where('transaction_history.credit_cost', '>', 0)
            //     ->where('transaction_history.updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
            //     ->where('transaction_history.updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
            //     ->select('clinic.Name as clinic_name', 'user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.transaction_id')
            //     ->groupBy('transaction_history.transaction_id')
            //     ->orderBy('transaction_history.updated_at', 'asc')
            //     ->get();

            //     foreach ($data as $key => $value) {
            //         if($value->credit_cost > 0 ) {
            //             $transaction_fee = (int)$value->procedure_cost * $value->medi_percent;
            //             $sum = (int)$value->credit_cost - $transaction_fee;
            //             if($sum > 0) {
            //                 array_push($array, $value);
            //             }
            //         }
            //     }
            // return $array;

        } else if($filter == 2) {
            $array = array();
            $data = DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
                ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.clinicID')
                ->where('transaction_history.paid', '=', 1)
                ->where('clinic.clinicID', '=', $clinicID)
                // ->where('transaction_history.credit_cost', '=', 0)
                ->where('transaction_history.updated_at', '>=', date("Y-m-d H:i:s", strtotime($start)))
                ->where('transaction_history.updated_at', '<=', date("Y-m-d H:i:s", strtotime($end)))
                ->select('clinic.Name as clinic_name', 'user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.transaction_id', 'transaction_history.clinic_discount')
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.updated_at', 'asc')
                ->get();

                foreach ($data as $key => $value) {
                    if($value->credit_cost > 0 ) {
                        $transaction_fee = (int)$value->procedure_cost * $value->medi_percent;
                        $sum = (int)$value->credit_cost - $transaction_fee;
                        if($sum < 0) {
                            array_push($array, $value);
                        }
                    }
                }

                return $array;
        }
    }

    function paymentTransactionHistory($start, $end, $search, $id)
    {

        // return strlen($search);
        if(strlen($search) > 0) {
            // return "using search";
            $data_1 = DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
                // ->where('transaction_history.ClinicID', '=', $id)
                // ->where('transaction_history.paid', '=', 1)
                // ->where('transaction_history.backdate_claim', 'false')
                // ->where('user.Name', 'like', '%'.$search.'%')
                // ->orWhere('doctor.Name', 'like', '%'.$search.'%')
                // ->orWhere('clinic_procedure.Name', 'like', '%'.$search.'%')
                ->where(function($query) use ($search, $id){
                    $query->where('transaction_history.clinicID', $id);
                    $query->where('transaction_history.paid', 1);
                    $query->where('transaction_history.backdate_claim', 0);
                    $query->where('user.Name', 'like', '%'.$search.'%');
                })
                ->orWhere(function($query) use ($search, $id){
                    $query->where('transaction_history.clinicID', $id);
                    $query->where('transaction_history.paid', 1);
                    $query->where('transaction_history.backdate_claim', 0);
                    $query->where('clinic_procedure.Name', 'like', '%'.$search.'%');
                })
                ->orWhere(function($query) use ($search, $id){
                    $query->where('transaction_history.clinicID', $id);
                    $query->where('transaction_history.paid', 1);
                    $query->where('transaction_history.backdate_claim', 0);
                    $query->where('doctor.Name', 'like', '%'.$search.'%');
                })
                ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.clinic_discount', 'transaction_history.date_of_transaction', 'transaction_history.date_of_transaction')
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.updated_at', 'asc')
                ->get();

            $data_2 = DB::table('transaction_history')
                ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_history.ProcedureID')
                // ->where('transaction_history.ClinicID', '=', $id)
                // ->where('transaction_history.paid', '=', 1)
                // ->where('transaction_history.backdate_claim', 'true')
                // ->where('user.Name', 'like', '%'.$search.'%')
                // ->orWhere('clinic_procedure.Name', 'like', '%'.$search.'%')
                ->where(function($query) use ($search, $id){
                    $query->where('transaction_history.clinicID', $id);
                    $query->where('transaction_history.paid', 1);
                    $query->where('transaction_history.backdate_claim', 1);
                    $query->where('user.Name', 'like', '%'.$search.'%');
                })
                ->orWhere(function($query) use ($search, $id){
                    $query->where('transaction_history.clinicID', $id);
                    $query->where('transaction_history.paid', 1);
                    $query->where('transaction_history.backdate_claim', 1);
                    $query->where('clinic_procedure.Name', 'like', '%'.$search.'%');
                })
                ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'clinic_procedure.Name as clinic_procedure_name', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.clinic_discount', 'transaction_history.date_of_transaction')
                ->groupBy('transaction_history.transaction_id')
                ->orderBy('transaction_history.updated_at', 'asc')
                ->get();

            return array_merge($data_1, $data_2);
        } else {
            // return "using date";
            $data_1 = DB::table('transaction_history')
                    ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                    ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                    ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                    ->join('doctor_procedure', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                    ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'doctor_procedure.ProcedureID')
                    ->where('transaction_history.ClinicID', '=', $id)
                    ->where('transaction_history.paid', '=', 1)
                    ->where('transaction_history.backdate_claim', 0)
                    ->where('transaction_history.date_of_transaction', '>=', date("Y-m-d H:i:s", strtotime($start)))
                    ->where('transaction_history.date_of_transaction', '<=', date("Y-m-d H:i:s", strtotime($end)))
                    ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'doctor.Name as doctor_name', 'clinic_procedure.Name as clinic_procedure_name', 'user_appoinment.Created_on', 'user_appoinment.BookDate', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.clinic_discount', 'transaction_history.date_of_transaction')
                    ->groupBy('transaction_history.transaction_id')
                    ->orderBy('transaction_history.date_of_transaction', 'asc')
                    ->get();

                $data_2 = DB::table('transaction_history')
                    ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
                    ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_history.ProcedureID')
                    ->where('transaction_history.ClinicID', '=', $id)
                    ->where('transaction_history.paid', '=', 1)
                    ->where('transaction_history.backdate_claim', 1)
                    ->where('transaction_history.date_of_transaction', '>=', date("Y-m-d H:i:s", strtotime($start)))
                    ->where('transaction_history.date_of_transaction', '<=', date("Y-m-d H:i:s", strtotime($end)))
                    ->select('user.Name', 'transaction_history.updated_at', 'transaction_history.procedure_cost', 'transaction_history.revenue', 'clinic_procedure.Name as clinic_procedure_name', 'transaction_history.credit_cost', 'transaction_history.medi_percent', 'transaction_history.current_wallet_amount', 'transaction_history.paid_medi', 'transaction_history.clinic_discount', 'transaction_history.date_of_transaction')
                    ->groupBy('transaction_history.transaction_id')
                    ->orderBy('transaction_history.date_of_transaction', 'asc')
                    ->get();
                return array_merge($data_1, $data_2);

            }
    }


    function checkAppointmentUpdateTransaction($clinicID, $user_id, $appointment_id, $procedure_id, $doctor_id)
    {
        $clinic = new Clinic( );
        $clinic_data = $clinic->getClinicPercentage($clinicID);
        $checkTransactionExistence = Transaction::where('ClinicID', '=', $clinicID)
                                                ->where('AppointmenID', '=', $appointment_id)
                                                ->where('ProcedureID', '=', $procedure_id)
                                                ->count();
        if($checkTransactionExistence > 0) {
            $clinic_procedure = DB::table('clinic_procedure')->where('ProcedureID', '=', $procedure_id)->first();
            $checkIfToBeUpdated = Transaction::where('AppointmenID', '=', $appointment_id)
                                            ->where('ProcedureID', '=', $procedure_id)
                                            ->where('DoctorID', '=', $doctor_id)
                                            ->count();

            if($checkIfToBeUpdated > 0) {
                return 0;
            } else {
                $wallet_id = DB::table('e_wallet')->where('UserID', '=', $user_id)->first();
                $data_1 = array(
                    'ClinicID'          => $clinicID,
                    'UserID'            => $user_id,
                    'ProcedureID'       => $procedure_id,
                    'procedure_cost'    => $clinic_procedure->Price,
                    'wallet_id'         => $wallet_id->wallet_id,
                    'DoctorID'          => $doctor_id,
                    'medi_percent'      => $clinic_data['medi_percent'],
                    'updated_at'        => Carbon::now()
                );

                Transaction::where('AppointmenID', '=', $appointment_id)
                            ->update($data_1);
            }
              // return "true";
        } else {
            $clinic_procedure = DB::table('clinic_procedure')->where('ProcedureID', '=', $procedure_id)->first();
            $wallet_id = DB::table('e_wallet')->where('UserID', '=', $user_id)->first();
            $data_2 = array(
                'ClinicID'          => $clinicID,
                'UserID'            => $user_id,
                'ProcedureID'       => $procedure_id,
                'AppointmenID'      => $appointment_id,
                'procedure_cost'    => $clinic_procedure->Price,
                'DoctorID'          => $doctor_id,
                'wallet_id'         => $wallet_id->wallet_id,
                'medi_percent'      => $clinic_data['medi_percent'],
                'date_of_transaction' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            );

            Transaction::create($data_2);

        }

    }

    public function updateToPaid($id)
    {
        return Transaction::where('transaction_id', '=', $id)->update(['paid_medi' => 1]);
    }

    public function FindAppointmentTransaction($id)
    {
        $data = Transaction::where('AppointmenID', '=', $id)->first();
        if($data) {
            return $data;
        } else {
            return FALSE;
        }
    }

    public function getDateTransaction($data)
    {
        // $start_date = strtotime($data['start_date']);
        // $end_date = strtotime($data['end_date']);

        $start_date = strtotime($data['start_date']);
        $end_date = strtotime($data['end_date']);
        $id = $data['clinic_id'];
        $data_1 = DB::table('transaction_history')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->where('user_appoinment.BookDate', '>=', $start_date)
                ->where('user_appoinment.BookDate', '<=', $end_date)
                ->where('transaction_history.paid', '=', 1)
                ->where('transaction_history.ClinicID', '=', $id)
                ->get();

        $end_date = date('Y-m-t ', $end_date);
        // $data_2 = DB::table('transaction_history')
        //         ->where('date_of_transaction', '>=', date('Y-m-01', $start_date))
        //         ->where('date_of_transaction', '<=', date('Y-m-d H:i:s', strtotime('+23 hours', strtotime($end_date))))
        //         ->where('paid', '=', 1)
        //         ->where('ClinicID', '=', $id)
        //         ->get();

        $data_2 = DB::table('transaction_history')
                ->where(function($query) use ($start_date, $end_date, $id){
                    $query->where('claim_date', '>=', date('Y-m-01', $start_date))
                    ->where('claim_date', '<=', date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($end_date))))
                    ->where('paid', '=', 1)
                    ->where('ClinicID', '=', $id);
                })
                ->orWhere(function($query) use ($start_date, $end_date, $id){
                    $query->where('created_at', '>=', date('Y-m-01', $start_date))
                    ->where('created_at', '<=', date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($end_date))))
                    ->where('paid', '=', 1)
                    ->where('ClinicID', '=', $id);
                })
                ->get();

                // ->join('')
        return array_merge($data_1, $data_2);
        // $result = Transaction::whereBetween('date_of_transaction', [$start_date, $end_date])
        //                     ->where('ClinicID', '=', $id)
        //                     ->where('paid', '=', 1)
        //                     ->get();
        // $result = Transaction::where('date_of_transaction', '>=', $start_date)
        //                     ->where('date_of_transaction', '>=', $end_date)
        //                     ->where('ClinicID', '=', $id)
        //                     ->where('paid', '=', 1)
        //                     ->get();
        // return $result;
    }

    public function getTransactionById($id)
    {
        $result = Transaction::where('transaction_id', '=', $id)->first();
        if($result != null) {
            return $result;
        }
    }

    public function checkTransaction($clinic_id, $data)
    {
        $start_date = strtotime(date('Y-m-01', strtotime($data['start_date'])));
        $end_date = SpendingInvoiceLibrary::getEndDate($data['start_date']);
        // return $end_date;
        $data = DB::table('transaction_history')
                ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
                ->where('user_appoinment.BookDate', '>=', $start_date)
                ->where('user_appoinment.BookDate', '<=', strtotime($end_date))
                ->where('transaction_history.paid', '=', 1)
                ->where('transaction_history.ClinicID', '=', $clinic_id)
                ->count();
        if($data == 0) {
            $start_date = date('Y-m-d', $start_date);
            // $end_date = date('Y-m-d', strtotime($end_date));
            return DB::table('transaction_history')
                ->where('date_of_transaction', '>=', $start_date)
                ->where('date_of_transaction', '<=', $end_date)
                ->where('paid', 1)
                ->where('ClinicID', $clinic_id)
                ->count();
        } else {
            return $data;
        }
    }

    // public function updateGP($id)
    // {
    //     return Transaction::where('ClinicID', $id)->update(['co_paid_status' => 1, 'co_paid_amount' => 13, 'medi_percent' => 0]);
    // }

    // public function updatePercent($id)
    // {
    //     return Transaction::where('ClinicID', $id)->update(['co_paid_status' => 0, 'co_paid_amount' => 0, 'clinic_discount' => 0, 'medi_percent' => 0]);
    // }

    public function getTransactionAppointments($id, $date)
    {
        return DB::table('transaction_history')
            ->join('user_appoinment', 'user_appoinment.UserAppoinmentID', '=', 'transaction_history.AppointmenID')
            ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_history.ProcedureID')
            ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
            ->where('transaction_history.ClinicID', $id)
            ->select('user.Name as customer_name', 'user_appoinment.BookDate as book_date', 'clinic_procedure.Name as procedure_name', 'transaction_history.procedure_cost as amount', 'transaction_history.transaction_id', 'user_appoinment.UserAppoinmentID as appointment_id', 'transaction_history.ClinicID as clinic_id', 'user_appoinment.StartTime', 'user_appoinment.EndTime')
            ->where('user_appoinment.Status', 0)
            ->where('user_appoinment.BookDate', '=', strtotime($date))
            ->get();
    }

    public function deleteTransaction($id)
    {   
        return Transaction::where('transaction_id', $id)->delete();
    }

    public function deleteFailedTransactionHistory($id)
    {
        return Transaction::where('transaction_id', $id)->delete();
    }

}
