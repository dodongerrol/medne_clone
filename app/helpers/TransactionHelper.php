<?php
class TransactionHelper
{
	
	public static function getClinicImageType($clinic_type)
	{
		$type = "";
      	$image = "";

		if($clinic_type->head == 1 || $clinic_type->head == "1") {
	        if($clinic_type->Name == "General Practitioner") {
	         $type = "General Practitioner";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
	       } else if($clinic_type->Name == "Dental Care") {
	         $type = "Dental Care";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
	       } else if($clinic_type->Name == "Traditional Chinese Medicine") {
	         $type = "Traditional Chinese Medicine";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
	       } else if($clinic_type->Name == "Health Screening") {
	         $type = "Health Screening";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
	       } else if($clinic_type->Name == "Wellness") {
	         $type = "Wellness";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
	       } else if($clinic_type->Name == "Health Specialist") {
	         $type = "Health Specialist";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
	       }
	    } else {
	        $find_head = DB::table('clinic_types')
	        ->where('ClinicTypeID', $clinic_type->sub_id)
	        ->first();
	        if($find_head->Name == "General Practitioner") {
	         $type = "General Practitioner";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
	       } else if($find_head->Name == "Dental Care") {
	         $type = "Dental Care";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
	       } else if($find_head->Name == "Traditional Chinese Medicine") {
	         $type = "Traditional Chinese Medicine";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
	       } else if($find_head->Name == "Health Screening") {
	         $type = "Health Screening";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
	       } else if($find_head->Name == "Wellness") {
	         $type = "Wellness";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
	       } else if($find_head->Name == "Health Specialist") {
	         $type = "Health Specialist";
	         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
	       }
	    }

	    return array('type' => $type, 'image' => $image);
	}

	public static function getCoPayment($clinic, $date, $user_id)
	{
		$peak_amount = 0;
		$consultation_fees = 0;

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
       	$consultation_fees = $peak_amount;
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

    return array('co_paid_amount' => $co_paid_amount, 'co_paid_status' => $co_paid_status, 'peak_amount' => $peak_amount, 'consultation_fees' => $consultation_fees);
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
}
?>