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
    $type_name = "";
    
    if($clinic_type->head == 1 || $clinic_type->head == "1") {
      if($clinic_type->Name == "GP") {
       $type = "GP";
       $type_name = "general_practitioner";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
     } else if($clinic_type->Name == "Dental") {
       $type = "Dental";
       $type_name = "dental_care";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
     } else if($clinic_type->Name == "TCM") {
       $type = "TCM";
       $type_name = "tcm";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
     } else if($clinic_type->Name == "Screening") {
       $type = "Screening";
       $type_name = "health_screening";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
     } else if($clinic_type->Name == "Wellness") {
       $type = "Wellness";
       $type_name = "wellness";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
     } else if($clinic_type->Name == "Specialist") {
       $type = "Specialist";
       $type_name = "health_specialist";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
     }
    } else {
      $find_head = DB::table('clinic_types')
      ->where('ClinicTypeID', $clinic_type->sub_id)
      ->first();
      if($find_head->Name == "GP") {
       $type = "GP";
       $type_name = "general_practitioner";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
     } else if($find_head->Name == "Dental") {
       $type = "Dental";
       $type_name = "dental_care";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
     } else if($find_head->Name == "TCM") {
       $type = "TCM";
       $type_name = "tcm";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
     } else if($find_head->Name == "Screening") {
       $type = "Screening";
       $type_name = "health_screening";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
     } else if($find_head->Name == "Wellness") {
       $type = "Wellness";
       $type_name = "wellness";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
     } else if($find_head->Name == "Specialist") {
       $type = "Specialist";
       $type_name = "health_specialist";
       $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
     }
    }

    return array('type' => $type, 'image' => $image, 'type_name' => $type_name);
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
        $owner_id = StringHelper::getUserId($findUserID);
        $customer_id = PlanHelper::getCustomerId($owner_id);
        if($customer_id) {
          foreach ($clinics as $key => $clinic) {
            $company_block = DB::table('company_block_clinic_access')
                          ->where('customer_id', $customer_id)
                          ->where('clinic_id', $clinic['clinic_id'])
                          ->where('account_type', 'company')
                          ->where('status', 1)
                          ->first();
            if($company_block) {
              unset($clinics[$key]);
            } else {
              $employee_block = DB::table('company_block_clinic_access')
                ->where('customer_id', $owner_id)
                ->where('clinic_id', $clinic['clinic_id'])
                ->where('account_type', 'employee')
                ->where('status', 1)
                ->first();

              if($employee_block) {
                unset($clinics[$key]);
              }
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
        $owner_id = StringHelper::getUserId($findUserID);
        $customer_id = PlanHelper::getCustomerId($owner_id);
        if($customer_id) {
          foreach ($clinics as $key => $clinic) {
            $block = DB::table('company_block_clinic_access')
                          ->where('customer_id', $customer_id)
                          ->where('clinic_id', $clinic->ClinicID)
                          ->where('account_type', 'company')
                          ->where('status', 1)
                          ->first();
            if($block) {
              unset($clinics[$key]);
            } else {
              $block = DB::table('company_block_clinic_access')
                          ->where('customer_id', $owner_id)
                          ->where('clinic_id', $clinic->ClinicID)
                          ->where('account_type', 'employee')
                          ->where('status', 1)
                          ->first();
              if($block) {
                unset($clinics[$key]);
              }
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

  public static function getDefaultService( )
  {
    $dataArray = array();
    $service = DB::table('clinic_procedure')->where('default_selection', 1)->first();
    $dataArray['procedureid'] = $service->ProcedureID;
    $dataArray['name'] = $service->Name;
    $dataArray['duration'] = $service->Duration.' '.$service->Duration_Format;
    $dataArray['price'] = $service->Price;
    return $dataArray;
  }

  public static function getServiceDetails($id)
  {
    $dataArray = array();
    $service = DB::table('clinic_procedure')->where('ProcedureID', $id)->first();
    if($service) {
      $dataArray['procedureid'] = $service->ProcedureID;
      $dataArray['name'] = $service->Name;
      $dataArray['duration'] = $service->Duration.' '.$service->Duration_Format;
      $dataArray['price'] = $service->Price;
    } else {
      return false;
    }
    
    return $dataArray;
  }
}
?>