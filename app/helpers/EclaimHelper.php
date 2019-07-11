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
        'currency_exchange_rate'  => 0,
        'currency_type'   => 'sgd'
      ),
      array(
        'currency_name'   => "MYR - Malaysian Ringgit",
        'currency_exchange_rate'  => 3.00,
        'currency_type'   => 'myr'
      )
    );

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
        'key'    => 'AKIAI6QVQUMRF7RN6T2Q',
        'secret' => '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',
      ],
    ]);

   return $s3->getObjectUrl('mednefits', "receipts/".$doc, '+120 minutes');
  }
}
?>