<?php
use Aws\S3\S3Client;

class EclaimHelper
{
	
	public static function checkPendingEclaims($user_ids, $type)
  {
    $amount = DB::table('e_claim')
              ->whereIn('user_id', $user_ids)
              ->where('status', 0)
              ->where('spending_type', $type)
              ->sum('amount');

    return $amount;
  }

  public static function getCurrencies( )
  {
    $data = array(
      array(
        'currency_name'   => "SGD - Singapore Dollar",
        'currency_exchange_rate'  => 3.00,
        'currency_type'   => 'sgd'
      ),
      array(
        'currency_name'   => "MYR - Malaysian Ringgit",
        'currency_exchange_rate'  => 3.00,
        'currency_type'   => 'myr'
      )
    );

    foreach ($data as $key => $curr) {
      $currency = DB::table('currency_options')->where('currency_type', $curr['currency_type'])->first();
      
      if($currency) {
        $curr['currency_exchange_rate'] = $currency->currency_value;
      }
    }

    return $data;
  }

  public static function sendEclaimEmail($user_id, $e_claim_id)
  {
    $email = [];
    $environment = Config::get('config.environment');
    $e_claim = DB::table('e_claim')->where('e_claim_id', $e_claim_id)->first();
    $user = DB::table('user')->where('UserID', $user_id)->first();
    
    $id = 'MNF'.str_pad($e_claim->e_claim_id, 6, "0", STR_PAD_LEFT);
    $submitted_date = date('F d, Y', strtotime($e_claim->created_at));
    $email['emailTo'] = $user->Email ? $user->Email : 'info@medicloud.sg';
    $email['emailName'] = ucwords($user->Name);
    $email['transaction_id'] = $id;
    $email['submitted_date'] = $submitted_date;
    
    if((int)$e_claim->status == 0) {
      $email['emailPage'] = 'email-templates.eclaim-submitted';
      $email['emailSubject'] = 'Claim '.$id.' Submitted on '.$submitted_date;
    } else if((int)$e_claim->status == 1) {
      $email['emailPage'] = 'email-templates.eclaim-approved';
      $email['emailSubject'] = 'Approved: Claim '.$id.' Submitted on '.$submitted_date;
    } else {
      $email['emailPage'] = 'email-templates.eclaim-rejected';
      $email['emailSubject'] = 'Rejected: Claim '.$id.' Submitted on '.$submitted_date;
    }

    
    if($environment == "production") {
      $email['url'] = "https://medicloud.sg";
    } else if($environment == "stage") {
      $email['url'] = "http://staging.medicloud.sg";
    } else {
      $email['url'] = "http://medicloud.local";
    }

    return EmailHelper::sendEmail($email);
  }

  public static function createPreSignedUrl($doc)
  {
    $s3 = S3Client::factory( [
      'region' => 'ap-southeast-1',
      'version' => 'latest',
      // 'endpoint' => 'https://mednefits.com',
      'bucket_endpoint' => true,
      'credentials' => [
        'key'    => 'AKIAXK5O7G5SO7RVV7ET',
        'secret' => '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',
      ],
    ]);

   return $s3->getObjectUrl('mednefits', "receipts/".$doc, '+120 minutes');
  }

  public static function getSpendingBalance($user_id, $date, $spending_type)
  {
    $wallet_table_logs = null;

    if($spending_type == "medical") {
      $wallet_table_logs = "wallet_history";
    } else if($spending_type == "wellness") {
      $wallet_table_logs = "wellness_wallet_history";
    } else {
      return array('status' => false, 'message' => 'spending_type must be medical or balance');
    }


    $get_allocation = 0;
    $deducted_credits = 0;
    $credits_back = 0;
    $deducted_by_hr_medical = 0;
    $in_network_temp_spent = 0;
    $e_claim_spent = 0;
    $deleted_employee_allocation = 0;
    $total_deduction_credits = 0;
    $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
    $wallet_id = $wallet->wallet_id;
    $start_date = null;
    $end_date = null;
    $back_date = false;

    $reset = DB::table('credit_reset')
                ->where('id', $user_id)
                ->where('spending_type', $spending_type)
                ->where('user_type', 'employee')
                ->get();
    // return $reset;
    
    $first_wallet_history = DB::table($wallet_table_logs)->where('wallet_id', $wallet_id)->first();
    $allocation_date = date('Y-m-d', strtotime($first_wallet_history->created_at));
    $temp_start_date = $allocation_date;

    if(sizeof($reset) > 0) {
      for( $i = 0; $i < sizeof( $reset ); $i++ ){
        $temp_end_date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $reset[$i]->date_resetted ) ) ));
        $temp_end_date = PlanHelper::endDate($temp_end_date);
        if( strtotime( $temp_start_date ) < strtotime($date) && strtotime($date) < strtotime( $temp_end_date ) ){
          $start_date = $temp_start_date;
          $end_date = $temp_end_date;
        }
        $temp_start_date = $reset[$i]->date_resetted;
        $back_date = true;
        if( $i == (sizeof( $reset )-1) ){
          if( $start_date == null && $end_date == null ){
            $back_date = false;
            $start_date = $temp_start_date;
            $end_date = date('Y-m-d',(strtotime ( '+1 day' , strtotime( date('Y-m-d') ))));
          }
        }
      }

      // return $start_date.' '.$end_date;
      $wallet_history = DB::table($wallet_table_logs)
              ->join('e_wallet', 'e_wallet.wallet_id', '=', $wallet_table_logs.'.wallet_id')
              ->where($wallet_table_logs.'.wallet_id', $wallet_id)
              ->where('e_wallet.UserID', $user_id)
              ->where($wallet_table_logs.'.created_at',  '>=', $start_date)
              ->where($wallet_table_logs.'.created_at',  '<=', $end_date)
              ->get();
      // return $wallet_history;
    } else {
      $wallet_history = DB::table($wallet_table_logs)->where('wallet_id', $wallet_id)->get();
    }
    
    // return $start_date.' - '.$end_date;

    foreach ($wallet_history as $key => $history) {
      if($history->logs == "added_by_hr") {
        $get_allocation += $history->credit;
      }

      if($history->logs == "deducted_by_hr") {
        $deducted_credits += $history->credit;
      }

      if($history->where_spend == "e_claim_transaction") {
        $e_claim_spent += $history->credit;
      }

      if($history->where_spend == "in_network_transaction") {
        $in_network_temp_spent += $history->credit;
      }

      if($history->where_spend == "credits_back_from_in_network") {
        $credits_back += $history->credit;
      }
    }

    $pro_allocation = DB::table($wallet_table_logs)
    ->where('wallet_id', $wallet_id)
    ->where('logs', 'pro_allocation')
    ->sum('credit');

    $get_allocation_spent_temp = $in_network_temp_spent - $credits_back;
    $get_allocation_spent = $get_allocation_spent_temp + $e_claim_spent;
    $medical_balance = 0;

    if($pro_allocation) {
      $allocation = $pro_allocation;
      $balance = $pro_allocation - $get_allocation_spent;
      $medical_balance = $balance;

      if($balance < 0) {
        $balance = 0;
        $medical_balance = $balance;
      }
    } else {
      $allocation = $get_allocation - $deducted_credits;
      $balance = $allocation - $get_allocation_spent;
      $medical_balance = $balance;
      $total_deduction_credits += $deducted_credits;
    }

    if($pro_allocation > 0) {
      $allocation = $pro_allocation;
    }

    return array('balance' => $balance, 'back_date' => $back_date);

    // return array('allocation' => $allocation, 'get_allocation_spent' => $get_allocation_spent, 'balance' => $balance >= 0 ? $balance : 0, 'e_claim_spent' => $e_claim_spent, 'in_network_spent' => $get_allocation_spent_temp, 'deleted_employee_allocation' => $deleted_employee_allocation, 'total_deduction_credits' => $total_deduction_credits, 'medical_balance' => $medical_balance, 'total_spent' => $get_allocation_spent);

    // return array('status' => true, 'data' => $reset);
  }
}
?>