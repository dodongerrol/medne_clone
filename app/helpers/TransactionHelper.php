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

	public static function getCoPayment($clinic, $consultation_fees)
	{
		$peak_amount = 0;
		$consultation_fees = $consultation_fees;

      // check clinic peak hours
       $result = ClinicHelper::getCheckClinicPeakHour($clinic, date('Y-m-d H:i:s'));
      if($result['status']) {
         $peak_amount = $result['amount'];
         $clinic_peak_status = true;
        // check user company peak status
         $user_peak = PlanHelper::getUserCompanyPeakStatus($user_id);
         if($user_peak) {
          if((int)$clinic->co_paid_status == 1) {
           $gst = $clinic->peak_hour_amount * $clinic->gst_percent;
           $co_paid_amount = $clinic->peak_hour_amount + $gst;
           $co_paid_status = $clinic->co_paid_status;
           $consultation_fees = $co_paid_amount;
         } else {
           $co_paid_amount = $clinic->peak_hour_amount;
           $co_paid_status = $clinic->co_paid_status;
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
    }

    	return array('co_paid_amount' => $co_paid_amount, 'co_paid_status' => $co_paid_status, 'peak_amount' => $peak_amount, 'consultation_fees' => $co_paid_amount);
	}
}
?>