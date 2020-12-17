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

  public static function checkPendingEclaimsByVisitDate($user_ids, $type, $date)
  {
    $amount = DB::table('e_claim')
              ->whereIn('user_id', $user_ids)
              ->where('date', '>=', $date)
              ->where('date', '<=', $date)
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

    // get user id and user token
    $owner_id = StringHelper::getUserId($user_id);

    $member = DB::table('user')->where('UserID', $owner_id)->first();
    $jwt = new \JWT();
		// create token
		$member->signed_in = FALSE;
		$member->expire_in = strtotime('+15 days', time());
		$secret = 'w2c5M]=JSE/tpj#4;X';
		$token = $jwt->encode($member, $secret);

    if($environment == "production") {
      $email['url'] = "https://medicloud.sg/app/login_empoyee_from_admin?token=".$token;
    } else if($environment == "stage") {
      $email['url'] = "http://staging.medicloud.sg/app/login_empoyee_from_admin?token=".$token;
    } else {
      $email['url'] = "http://medicloud.local/app/login_empoyee_from_admin?token=".$token;
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

   return $s3->getObjectUrl('mednefits', "receipts/".$doc, '+60 minutes');
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

    $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
    $wallet_id = $wallet->wallet_id;
    $start_date = null;
    $end_date = null;
    $back_date = false;
    $first_plan = PlanHelper::getUserFirstPlanByCreatedAt($user_id);
    $reset = DB::table('credit_reset')
                ->where('id', $user_id)
                ->where('spending_type', $spending_type)
                ->where('user_type', 'employee')
                ->orderBy('created_at', 'asc')
                ->get();
    
    if(sizeof($reset) > 0) {
      $temp_end_date = date('Y-m-d');
      $temp_end_date = PlanHelper::endDate($temp_end_date);
      foreach ($reset as $key => $res) {
        if( strtotime( $date ) > strtotime( $res->date_resetted ) ){
          $start_date = $res->date_resetted;
        }
        if( strtotime( $date ) < strtotime( $res->date_resetted ) ){
          $end_date = $end_date == null ? $res->date_resetted : $end_date;
        }

        $back_date = $end_date != null ? true : false;
        if( $key == (sizeof( $reset )-1) ){
          if( $start_date == null ){
            $start_date = $wallet->created_at;
          }
          if( $end_date == null ){
            $end_date = $temp_end_date;
          }
          $end_date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $end_date ) ) ));
          $end_date = PlanHelper::endDate($end_date);
        }
      }
    }else{
      $start_date = $first_plan;
      $end_date = date('Y-m-d H:i:s');
    }
    
    if($spending_type == "medical") {
      $result = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $user_id, $start_date, $end_date);
    } else {
      $result = PlanHelper::memberWellnessAllocatedCreditsByDates($wallet->wallet_id, $user_id, $start_date, $end_date);
    }
    $result['back_date'] = $back_date;
    $result['currency_type'] = strtoupper($wallet->currency_type);
    return $result;
    return ['start' => $start_date, 'end' => $end_date];
    $user = DB::table('user')->where('UserID', $user_id)->first();
    $first_wallet_history = DB::table($wallet_table_logs)->where('wallet_id', $wallet_id)->first();
    $allocation_date = date('Y-m-d', strtotime($wallet->created_at));

    if(sizeof($reset) > 0) {
      $start_temp = strtotime($date);
      $default_start = false;
      for( $i = 0; $i < sizeof( $reset ); $i++ ){
        $date_resetted = strtotime($reset[$i]->date_resetted);

        if($start_temp < $date_resetted) {
          $default_start = false;
          // get lastest credit reset
          $latest_reset = DB::table('credit_reset')
                ->where('id', $user_id)
                ->where('spending_type', $spending_type)
                ->where('user_type', 'employee')
                ->orderBy('created_at', 'desc')
                ->first();

          if(strtotime($latest_reset->date_resetted) > $date_resetted) {
            $start_date = date('Y-m-d', $date_resetted);
            $end_date = date('Y-m-d', strtotime($latest_reset->date_resetted));
            $end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_date)));
            $back_date = true;
          } else {
            $start_date = date('Y-m-d', strtotime($first_plan));
            $end_date = date('Y-m-d', $date_resetted);
            $end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_date)));
            $back_date = true;
          }
        } else {
          $default_start = true;
          $start_date = date('Y-m-d', strtotime($first_plan));
          $end_date = date('Y-m-d', strtotime('-1 day'));
        }
      }
      
      if($start_date > $end_date) {
        $end_date = $start_date;
      }
      $end_date = PlanHelper::endDate($end_date);
      return $start_date.' - '.$end_date;
      $wallet_history = DB::table($wallet_table_logs)
              ->join('e_wallet', 'e_wallet.wallet_id', '=', $wallet_table_logs.'.wallet_id')
              ->where($wallet_table_logs.'.wallet_id', $wallet_id)
              ->where('e_wallet.UserID', $user_id)
              ->where($wallet_table_logs.'.created_at',  '>=', $start_date)
              ->where($wallet_table_logs.'.created_at',  '<=', $end_date)
              ->get();
    } else {
      $wallet_history = DB::table($wallet_table_logs)->where('wallet_id', $wallet_id)->get();
    }

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

      if($user->Active == 0) {
        $deleted_employee_allocation = $get_allocation - $deducted_credits;
        $medical_balance = 0;
      }
    }
    // else {
    //   if($spending_type == "medical") {
    //     $result = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet_id, $user_id, $start_date, $end_date);
    //     return array('balance' => (float)$result['balance'], 'back_date' => $back_date, 'last_term' => $back_date, 'allocation' => $result['allocation'], 'in_network_spent' => $result['in_network_spent'], 'e_claim_spent' => $result['e_claim_spent'], 'total_spent' => $result['get_allocation_spent'], 'currency_type' => strtoupper($wallet->currency_type));
    //   } else {
    //     $result = PlanHelper::memberWellnessAllocatedCreditsBydates($wallet_id, $user_id, $start_date, $end_date);
    //     return array('balance' => (float)$result['balance'], 'back_date' => $back_date, 'last_term' => $back_date, 'allocation' => $result['allocation'], 'in_network_spent' => $result['in_network_spent'], 'e_claim_spent' => $result['e_claim_spent'], 'total_spent' => $result['get_allocation_spent'], 'currency_type' => strtoupper($wallet->currency_type));
    //   }
      
    // }

    return array('balance' => (float)$balance, 'back_date' => $back_date, 'last_term' => $back_date, 'allocation' => $allocation, 'in_network_spent' => $get_allocation_spent_temp, 'e_claim_spent' => $e_claim_spent, 'total_spent' => $get_allocation_spent, 'currency_type' => strtoupper($wallet->currency_type));
  }

  public static function checkMemberClaimAEstatus($member_id)
  {
    $plan_status = PlanHelper::checkEmployeePlanStatus($member_id);
    $start = date('Y-m-d', strtotime($plan_status['start_date']));
    $end = date('Y-m-d', strtotime($plan_status['valid_date']));
    $end = PlanHelper::endDate($end);
    $ids = StringHelper::getSubAccountsID($member_id);

    $claim = DB::table('e_claim')
                ->where('service', 'Accident & Emergency')
                ->whereIn('user_id', $ids)
                ->where('status', 1)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->get();
                
    if(sizeof($claim) >= 2)  {
      return true;
    } else {
      return false;
    }
  }
}
?>