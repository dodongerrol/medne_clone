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

   return $s3->getObjectUrl('mednefits', "receipts/".$doc, '+60 minutes');
  }

  public static function getSpendingBalance($user_id, $date, $spending_type)
  {
    $customerId = StringHelper::getCustomerId($user_id);
    $spending = CustomerHelper::getAccountSpendingStatus($customerId);
    $wallet_table_logs = null;
    $spending_method = null;

    if($spending_type == "medical") {
      $wallet_table_logs = "wallet_history";
      $spending_method = $spending['medical_payment_method_non_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';
    } else if($spending_type == "wellness") {
      $wallet_table_logs = "wellness_wallet_history";
      $spending_method = $spending['wellness_payment_method_non_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';
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
                // ->where('spending_method', $spending_method)
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
    $result['spending_method'] = $spending_method;
    return $result;
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