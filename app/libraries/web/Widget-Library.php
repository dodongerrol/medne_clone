<?php
/**
* nhr
*/
use Carbon\Carbon;
class Widget_Library {

	function __construct()
	{
		# code...
	}


	// get clinic base on search key name
	public static function getClinicByName($clinicName)
	{
		return DB::table('clinic')->where('Name', 'LIKE', '%'.$clinicName.'%')->get();
	}

	// for medical partners only
	public static function getClinicMedicalPartners($clinic_name) {
		$medical_partners = ['The Clinic @ Aperia', 'The Clinic @ Fusionopolis', 'The Clinic @ Capitagreen', 'The Clinic @ One George Street', 'The Clinic @ Campus', 'Clinic @ Business City'];
		$only_aesthetics = ["Only Group (Raffles Place)", "Only Group (Capitol Piazza)", "Only Group (I12 Katong)", "Only Group (Holland Village)", "Only Group (Pacific Plaza)", "Only Group (Orchard Road)", "Only Group (Dhoby Ghaut)"];
		$dental_clinic = ["Dental Focus Capitol Clinic", "Dental Focus People's Park Clinic", "Urban Dental London (Chinatown Point)"];
		$cmc = ["CMC (Yishun)", "CMC 中国中医 (Bugis)", "CMC 中国中医 (Bukit Merah)", "CMC 中国中医 (Jurong East)", "CMC 中国中医 (Choa Chu Kang)", "CMC 中国中医 (Jurong West)", "CMC 中国中医 (Eunos)", "CMC 中国中医 (Hougang)", "CMC 中国中医 (Tampines)", "CMC 中国中医 (Punggol)"];
		if($clinic_name == 'medical_partners') {
				return DB::table('clinic')->whereIn('Name', $medical_partners)->where('Active', 1)->get();
		} else if($clinic_name == 'only_group') {
				return DB::table('clinic')->whereIn('Name', $only_aesthetics)->where('Active', 1)->get();
		} else if($clinic_name == 'dental_focus') {
			return DB::table('clinic')->whereIn('Name', $dental_clinic)->where('Active', 1)->get();
		} else if ($clinic_name == 'cmc') {
			return DB::table('clinic')->whereIn('Name', $cmc)->where('Active', 1)->get();
		}
	}

// get clinic data only
	public static function getClinicDataOnly($clinicid)
	{
		StringHelper::Set_Default_Timezone();
    //     $currentdate = date('d-m-Y');
    //     $allDoctors = Clinic_Library::FindAllClinicDoctors($clinicid);
    //     $findClinicDetails = Clinic_Library::FindClinicDetails($clinicid);
 	// 	 $findClinicprocedures = Clinic_Library::FindClinicProcedures($clinicid);
		 //
    //     $dataArray['doctors'] = $allDoctors;
    //     $dataArray['procedure'] = $findClinicprocedures;
    //     $dataArray['clincID'] = $clinicid;
    //     $dataArray['clincname'] = $findClinicDetails->Name;
    //     $dataArray['title'] = "Doctor widget";
				//
        // return $dataArray;
				return DB::table('clinic')->select('clinic.ClinicID', 'clinic.Name', 'clinic.Address', 'clinic.City', 'clinic.Country', 'clinic.Lat', 'clinic.Lng')->skip(2000)->take(6)->get();
	}

	public static function getClinicData($clinicid)
	{
		StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $allDoctors = Clinic_Library::FindAllClinicDoctors($clinicid);
        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicid);
 		$findClinicprocedures = Clinic_Library::FindClinicProcedures($clinicid);

        $dataArray['doctors'] = $allDoctors;
        $dataArray['procedure'] = $findClinicprocedures;
        $dataArray['clincID'] = $clinicid;
        $dataArray['clincname'] = $findClinicDetails->Name;
        $dataArray['title'] = "Doctor widget";
        $view = View::make('widget.doctor-widget',$dataArray);
       	return $view;
	}

	public static function loadDoctorProcedure()
	{
		 $allInputs = Input::all();
		 $doctorID = $allInputs['docID'];
		 $clinicID = $allInputs['clinicID'];

		return $doctorProcedures = Doctor_Library::FindDoctorProcedures($clinicID,$doctorID);
	}

    public static function loadProcedureDoctor()
    {   
         $allInputs = Input::all();
         $procedureID = $allInputs['procedureID'];
         $clinicID = $allInputs['clinicID'];

         $dp = new DoctorProcedures();
         $doctorlist = $dp->FindDoctorsByProcedure($procedureID, $clinicID);

         return $doctorlist;

    }

    //  public static function loadProcedureDoctor()
    // {
    //      $allInputs = Input::all();
    //      $procedureID = $allInputs['procedureID'];
    //      $clinicID = $allInputs['clinicID'];

    //      $dp = new DoctorProcedures();
    //      $doctorlist = $dp->FindDoctorsByProcedure($procedureID, $clinicID);

    //      return $doctorlist;

    // }


	public static function loadEndTime()
	{
		  $allInputs = Input::all();
        StringHelper::Set_Default_Timezone();
		 $time = strtotime($allInputs['time']);
		 $duration = $allInputs['duration'];

		$findEndTime = String_Helper_Web::FindEndTime($time,$duration);

		return $findEndTime;

	}


    public static function checkBooking($date, $start, $end, $doc_id, $clinic_id)
    {
        $dataArray = array('doctor_id' => $doc_id, 'type' => 3);
        $breaks = new ExtraEvents();
        $breaks = $breaks->getDoctorBreaks($dataArray);
        $times = [];
        $h = 0;
        while ($h < 24) {
            $key = date('H:i', strtotime(date('Y-m-d') . ' + ' . $h . ' hours'));
            $value = date('h:i A', strtotime(date('Y-m-d') . ' + ' . $h . ' hours'));
            if($start <= strtotime($value) && $end >= strtotime($value)) {
                $times[] = $value;
            }
            $h++;
        }
        // return $times;
        // $ClinicTimes = self::getClinicAvailablity($clinic_id);
        // $DoctorTimes = General_Library::FindAllClinicTimesNew(2,$doc_id,strtotime(date('d-m-Y')));
        foreach ($breaks as $value) {

            // $stime = date("H:i", strtotime($value->start_time));
            // $etime = date("H:i", strtotime($value->end_time));
            $day = $value->day;
            $day_2 = date('D', $date);
            if($day == strtolower($day_2)) {
                // foreach ($times as $key => $time) {
                //    if(strtotime($value->start_time) >= strtotime($time) && strtotime($value->end_time) < strtotime($time)) {
                //         return 3;
                //    }
                // }
                if($start >= strtotime($value->start_time) && $start < strtotime($value->end_time)) {
                    return 3;
                } else if($end >= strtotime($value->start_time) && $end < strtotime($value->end_time)) {
                    return 3;
                }
                // break;
            }
        }
    }

    public static function getClinicAvailablity($clinicid)
    {
        $input = Input::all();
        $start_date = date('Y-m-d');//$input['current_date'];

        $findClinicTimes = General_Library::FindAllClinicTimes(3,$clinicid,strtotime($start_date));
        // foreach ($findDoctorTimes as $value) {
        //  echo $value->StartTime;
        // }

        return $findClinicTimes;

    }

	public static function NewClinicAppointment($clinicdata){

        $allInputs = Input::all();
        // if(isset($allInputs['user_id'])) {
        //     return "true";
        // } else {
        //     return "false";
        // }
        // StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');

        $allInputs['starttime'] = strtotime($allInputs['starttime']);
        $allInputs['endtime'] = strtotime($allInputs['endtime']);
        // return $allInputs['starttime'].'-'.strtotime(date('h:i A'));
        if( date('Y-m-d', strtotime($allInputs['bookdate'])) == date('Y-m-d')) {
            // .date('h:i A',$allInputs['starttime']))
            if($allInputs['starttime'] < strtotime(date('h:i A'))) {
                return 3;
            }
        }

        $clinic = new Clinic( );

        $userexistStatus = 0;
        $clinic->checkCoPaidAmount($clinicdata->Ref_ID);
        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            // $findPlusSign = substr($allInputs['phone'], 0, 1);
            // if($findPlusSign == 0){
            //     $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
            // }else{
            //     $PhoneOnly = $allInputs['code'].$allInputs['phone'];
            // }
            $temp = explode(str_replace('+', '', $allInputs['code']), $allInputs['phone']);
            $number = $temp[sizeof($temp) - 1];
            $PhoneOnly = $allInputs['code'].$number;
            //$findUser = Auth_Library::FindRealUser($allInputs['nric'],$allInputs['email']);
            if(isset($allInputs['user_id'])) {
                $user = new User();
                $findUser = $user->getUserById($allInputs['user_id']);
                $check_book = self::checkBooking(strtotime($allInputs['bookdate']), $allInputs['starttime'], $allInputs['endtime'], $allInputs['doctorid'], $clinicdata->Ref_ID);
                if($check_book == 3) {
                    return 3;
                }
            } else {
                $findUser = Auth_Library::FindUserEmail($allInputs['email']);
            }

            // return $findUser;

            if($findUser){
                //$userid = $findUser->UserID;
                $userid = $findUser;
                $userexistStatus = 1;
            }else{
                $pw = StringHelper::get_random_password(8);

                $userData['name'] = $allInputs['name'];
                $userData['usertype'] = 1;
                $userData['email'] = $allInputs['email'];
                // $userData['nric'] = $allInputs['nric'];
                $userData['code'] = $allInputs['code'];
                $userData['mobile'] = $PhoneOnly;

                $userData['address'] = '';
                $userData['city'] = '';
                $userData['state'] = '';
                $userData['zip'] = '';

                $userData['ref_id'] = 0;
                $userData['activelink'] = null;
                $userData['status'] = 0;
                $userData['source'] = 3;//widget;
                $userData['pw'] = StringHelper::encode($pw);

                $newuser = Auth_Library::AddNewUser($userData);
                if($newuser){
                    $userid = $newuser;
                    // nhr 2016/7/25

                    // $emailDdata['emailPage']= 'email-templates.welcome';
                    // $emailDdata['emailTo']= $allInputs['email'];
                    // $emailDdata['pw']= $pw;
                    // $emailDdata['emailName']= $allInputs['name'];
                    // $emailDdata['emailSubject'] = 'Welcome to Mednefits';
                    // EmailHelper::sendEmail($emailDdata);
// dd($emailDdata);
                }else{
                    return 0;
                }
            }
            $wallet = new Wallet( );
            $procedure = new ClinicProcedures( );
            $transaction_data = new Transaction( );

            $getProcedure = $procedure->ClinicProcedureByID($allInputs['procedureid']);

            $wallet_id = $wallet->getWalletId($userid);
            
            $bookingtime = $allInputs['endtime'] - $allInputs['starttime'];
            $slottime = abs($bookingtime)/60;


            //$existingAppointment = General_Library::FindExistingAppointment($allInputs['doctorid'],strtotime($allInputs['bookdate']),$allInputs['starttime'],$allInputs['endtime']);
            $existingAppointment = General_Library::FindExistingAppointment($allInputs['doctorid'],strtotime($allInputs['bookdate']));

            $activeAppointment = 0;
            if($existingAppointment){
                foreach($existingAppointment as $appointExist){
                    if($activeAppointment ==0){
                        if(($appointExist->StartTime <= $allInputs['starttime'] && $appointExist->EndTime > $allInputs['starttime']) || ($appointExist->StartTime < $allInputs['endtime'] && $appointExist->EndTime >= $allInputs['endtime'])){
                            $activeAppointment = 1;
                        }
                    }
                }
            }

            if($activeAppointment==1 || ($activeAppointment !=1 && $slottime != $allInputs['duration'])){
                return 1;
            }
            //return 1;
            $starttime = date('h:i A',$allInputs['starttime']);
            $endtime = date('h:i A',$allInputs['endtime']);
            $bookArray['userid'] = $userid;
            $bookArray['clinictimeid'] = $allInputs['clinictimeid'];
            $bookArray['doctorid'] = $allInputs['doctorid'];
            $bookArray['procedureid'] = $allInputs['procedureid'];
            //$bookArray['starttime'] = $allInputs['starttime'];
            //$bookArray['endtime'] = $allInputs['endtime'];
            $bookArray['starttime'] = strtotime($allInputs['bookdate'].$starttime);
            $bookArray['endtime'] = strtotime($allInputs['bookdate'].$endtime);
                //$bookArray['slotplace'] = $allInputs['slotplace'];
            $bookArray['remarks'] = $allInputs['remarks'];
            $bookArray['bookdate'] = strtotime($allInputs['bookdate']);
            $bookArray['mediatype'] = 1;
            $bookArray['patient']=$allInputs['name'];
            $bookArray['price']=$allInputs['price'];

            $s = strtotime($allInputs['bookdate'].$starttime);
            $e = strtotime($allInputs['bookdate'].$endtime);

            $duration = ($e-$s)/60;
            $bookArray['duration']=$duration;
            // $bookArray['duration']=0;

            $newBooking = General_Library::NewAppointment($bookArray);
            if($newBooking){
                $findUserDetails = Auth_Library::FindUserDetails($userid);
                $findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
                $findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);
                //Update User Details
                if($userexistStatus==1){
                    $userupdate['userid'] = $findUserDetails->UserID;
                    // $userupdate['NRIC'] = $allInputs['nric'];
                    $userupdate['Name'] = $allInputs['name'];
                    $userupdate['PhoneCode'] = $allInputs['code'];
                    $userupdate['PhoneNo'] = $PhoneOnly;
                    Auth_Library::UpdateUsers($userupdate);
                }
                //Send SMS
                if(StringHelper::Deployment()==1){
                    // $smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$allInputs['bookdate']." from ".date('h:i A',$allInputs['starttime'])." to ".date('h:i A',$allInputs['endtime']).". Thank you for using medicloud.";
                    // if(strlen($findUserDetails->PhoneNo) > 8) {
                    //    $smsMessage = "Hello ".$findUserDetails->Name." your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is confirmed for ".date('d-m-Y',strtotime($allInputs['bookdate'])).", ".date('h:i A',$allInputs['starttime']).". Your appointment ID is: ".$newBooking.", thank you for using Mednefits. Get the free app at mednefits.com";

                    //     $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);

                    //     // $smsMessage_2 = "Hello ".$findUserDetails->Name.", your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is tomorrow at ".date('h:i A', $allInputs['starttime']).". Thank you for using Mednefits. Get the free app at mednefits.sg ";

                    //     // $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage_2);

                    //     $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $allInputs['name'], $allInputs['code'], $PhoneOnly, $smsMessage);
                    // }
                }

                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }
                //Send Email User
                $formatDate = date('l, j F Y',strtotime($allInputs['bookdate']));
                $emailDdata['bookingid'] = $newBooking;
                $emailDdata['remarks'] = $allInputs['remarks'];
                $emailDdata['bookingTime'] = date('h:i A',$allInputs['starttime']).' - '.date('h:i A',$allInputs['endtime']);
                $emailDdata['bookingNo'] = 0;
                $emailDdata['bookingDate'] = $formatDate;
                $emailDdata['doctorName'] = $findDoctorDetails->Name;
                $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                $emailDdata['clinicName'] = $findClinicDetails->Name;
                $emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
                $emailDdata['clinicPhone'] = $findClinicDetails->Phone;
                $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                $emailDdata['clinicProcedure'] = $procedurename;
                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPhone']= $findUserDetails->PhoneNo;
                $emailDdata['emailPage']= 'email-templates.booking';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Booking Confirmed';
                $api = 'mednefits.local/send_booking_notification';

                if(StringHelper::Deployment()==1){
                    // return httpLibrary::postHttp($api, $emailDdata, []);
                    EmailHelper::sendEmail($emailDdata);
                }
                //copy to company
                $emailDdata['emailTo']= Config::get('config.booking_email');
                if(StringHelper::Deployment()==1){
                    EmailHelper::sendEmail($emailDdata);
                }
                //Send email to Doctor
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $findDoctorDetails->Email;
                if(StringHelper::Deployment()==1){
                    EmailHelper::sendEmail($emailDdata);
                }
                //Send email to Clinic
                $emailDdata['emailPage']= 'email-templates.booking';
                $emailDdata['emailTo']= $clinicdata->Email;

                if(StringHelper::Deployment()==1){
                 EmailHelper::sendEmail($emailDdata);
                }

                $event_id = Clinic_Library::insertGoogleCalenderAppointment($bookArray,$findDoctorDetails); //nhr
                $ua = new UserAppoinment();
                $clinic = new Clinic( );
                if(isset($allInputs['user_id'])) {
                    $event_type = 0; //web;
                } else {
                    $event_type = 3; //widget;
                }
                $ua->updateUserAppointment(array('event_type'=> $event_type,'Gc_event_id'=>$event_id),$newBooking);
                // save transaction data

                if($findClinicDetails->co_paid_status == 1) {
                    $co_paid_amount = $findClinicDetails->co_paid_amount;
                    $co_paid_status = 1;
                } else {
                    $co_paid_amount = $findClinicDetails->co_paid_amount;
                    $co_paid_status = 0;
                }

                $discount = $clinic->getClinicPercentage($allInputs['clinicID']);
                $transaction = array(
                    'wallet_id'             => $wallet_id,
                    'ClinicID'              => $allInputs['clinicID'],
                    'UserID'                => $userid,
                    'ProcedureID'           => $allInputs['procedureid'],
                    'DoctorID'              => $allInputs['doctorid'],
                    'AppointmenID'          => $newBooking,
                    'procedure_cost'        => $getProcedure->Price,
                    'revenue'               => null,
                    'debit'                 => null,
                    'medi_percent'          => $discount['medi_percent'],
                    'clinic_discount'       => $discount['discount'],
                    'co_paid_amount'        => $co_paid_amount,
                    'co_paid_status'        => $co_paid_status,
                    'date_of_transaction'   => Carbon::now(),
                    'created_at'            => Carbon::now(),
                    'updated_at'            => Carbon::now()
                );

                $transaction_data->createTransaction($transaction);

                return $newBooking;


            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


}
 ?>
