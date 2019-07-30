<?php

class ClinicHelper {
  public static function getCheckClinicPeakHour($clinic, $date)
  {
    $day = strtolower(date('D', strtotime($date)));
    
    $clinic_peak = DB::table('clinic_peak')
                    ->where('clinic_id', $clinic->ClinicID)
                    ->where('day', $day)
                    ->where('active', 1)
                    ->first();
    
    if($clinic_peak) {
        if($clinic_peak->type == "full") {
            return array('status' => true, 'amount' => $clinic_peak->amount);
        } else {
            $start = strtotime(date('h:ia', $clinic_peak->start));
            $end = strtotime(date('h:ia', $clinic_peak->end));
            $time = strtotime(date('h:ia', strtotime($date)));
            $peak_status = $time >= $start && $time <= $end;

            if($peak_status) {
                return array('status' => true, 'amount' => $clinic_peak->amount);
            }
        }
    }

    $month = strtolower(date('M', strtotime($date)));
    $day = date('j', strtotime($date));

    $holiday =  DB::table('clinic_peak_holiday')
                ->where('clinic_id', $clinic->ClinicID)
                ->where('day_number', $day)
                ->where('month', $month)
                ->where('active', 1)
                ->first();

    if($holiday) {
        if($holiday->type == "full") {
            return array('status' => true, 'amount' => $holiday->amount);
        } else {
            $start = $holiday->start;
            $end = $holiday->end;
            $time = strtotime(date('h:ia', strtotime($date)));
            $peak_status = $time >= $start && $time <= $end;

            if($peak_status) {
                return array('status' => true, 'amount' => $holiday->amount);
            }
        }
    }

    return array('status' => false);
}

public static function sendClinicPeakStatus($data)
{
	return Mail::send('email-templates.clinic-peak-hours', $data, function($message) use ($data){
      $message->from('noreply@medicloud.sg', 'MediCloud');
      $message->to($data['to'], 'MediCloud');
      $message->subject($data['subject']);
  	});
}

public static function getClinicTypeImage($clinic_type)
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

  public static function removeBlockClinics($clinics)
  {
    $AccessToken = new Api_V1_AccessTokenController();
    $authSession = new OauthSessions();
    $getRequestHeader = StringHelper::requestHeader();

    if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);

      if($getAccessToken){
        $findUserID = $authSession->findUserID($getAccessToken->session_id);

        $customer_id = PlanHelper::getCustomerId($findUserID);
        if($customer_id) {
          foreach ($clinics as $key => $clinic) {
            $block = DB::table('company_block_clinic_access')
                          ->where('customer_id', $customer_id)
                          ->where('clinic_id', $clinic['clinic_id'])
                          ->where('status', 1)
                          ->first();
            if($block) {
              unset($clinics[$key]);
            }
          }
        }
      }
    }

    $format = [];

    foreach ($clinics as $key => $clinic) {
      array_push($format, $clinic);
    }

    return $format;
  }

  public static function removeBlockClinicsFromPaginate($clinics)
  {
    $AccessToken = new Api_V1_AccessTokenController();
    $authSession = new OauthSessions();
    $getRequestHeader = StringHelper::requestHeader();

    if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);

      if($getAccessToken){
        $findUserID = $authSession->findUserID($getAccessToken->session_id);

        $customer_id = PlanHelper::getCustomerId($findUserID);
        if($customer_id) {
          foreach ($clinics as $key => $clinic) {
            $block = DB::table('company_block_clinic_access')
                          ->where('customer_id', $customer_id)
                          ->where('clinic_id', $clinic->ClinicID)
                          ->where('status', 1)
                          ->first();
            if($block) {
              unset($clinics[$key]);
            }
          }
        }
      }
    }

    $format = [];

    foreach ($clinics as $key => $clinic) {
      array_push($format, $clinic);
    }

    return $format;
  }
}
?>