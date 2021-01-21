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
        'key'    => 'AKIAXK5O7G5SEZSHX7JU',
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
                ->groupBy('date_resetted')
                ->orderBy('created_at', 'asc')
                ->get();
    // return $reset;
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
          $end_date = date('Y-m-d',strtotime ( $end_date  ));
          $end_date = PlanHelper::endDate($end_date);
        }
      }
    }else{
      $start_date = $first_plan;
      $end_date = date('Y-m-d H:i:s');
    }
    // return ['start' => $start_date, 'end' => $end_date];
    if($spending_type == "medical") {
      $result = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $user_id, $start_date, $end_date);
    } else {
      $result = PlanHelper::memberWellnessAllocatedCreditsByDates($wallet->wallet_id, $user_id, $start_date, $end_date);
    }
    $result['back_date'] = $back_date;
    $result['currency_type'] = strtoupper($wallet->currency_type);
    return $result;
    return ['start' => $start_date, 'end' => $end_date];
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