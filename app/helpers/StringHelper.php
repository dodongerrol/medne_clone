<?php
require $_SERVER['DOCUMENT_ROOT'] . '/twilio-php/Twilio/autoload.php';
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Input;
use League\OAuth2\Server\Util\KeyAlgorithm\DefaultAlgorithm;
use League\OAuth2\Server\Util\KeyAlgorithm\KeyAlgorithmInterface;

class StringHelper{

    protected static $algorithm;

	public static function encode($password)
	{
		return md5($password);
	}
    public static function getEncryptValue(){
        $encryptValue = sha1(mt_rand(100000,999999).time());
        return $encryptValue;
    }

        public static function requestHeader()
	{
            /*
                Description: 
                    For Third Party API
                Developer: 
                    Stephen
                Date of refactor:
                    April 9, 2020
            */ 
            $thirdPartyAuthorization = '';
            $getRequestHeader = getallheaders();

            if (
                (!empty($getRequestHeader['X-ACCESS-KEY']) && !empty($getRequestHeader['X-MEMBER-ID']))
                || (!empty($getRequestHeader['x-access-key']) && !empty($getRequestHeader['x-member-id']))
            ) {
                $getRequestHeader['Authorization'] = self::verifyXAccessKey();
                
            } else {
                if(!empty($getRequestHeader['authorization']) && $getRequestHeader['authorization'] != null) {
                    $getRequestHeader['Authorization'] = $getRequestHeader['authorization'];
                }
            }
            
            return $getRequestHeader;
	}
        public static function errorMessage($type)
	{
            if($type == "Login"){
                return $message = "Invalid Login";
            }elseif($type=="Register"){
                return $message = "Registration Failed";
            }elseif($type=="Forgot"){
                return $message = "User not found";
            }elseif($type=="EmailExist"){
                return $message = "User email not found";
            }elseif($type=="EmailEmpty"){
                return $message = "Empty email is not allowed";
            }elseif($type=="EmptyValues"){
                return $message = "Cannot accept null values";
            }elseif($type=="NoRecords"){
                return $message = "No records found";
            }elseif($type=="logout"){
                return $message = "Successfully logged out";
            }elseif($type=="logerror"){
                return $message = "There was a problem while log out";
            }elseif($type=="Token"){
                return $message = "Your token is expired";
            }elseif($type=="Update"){
                return $message = "There was a problem while update";
            }elseif($type=="Tryagain"){
                return $message = "There was a problem! Please try again";
            }elseif($type=="QueueBlock"){
                return $message = "Queue Stopped";
            }elseif($type=="Deleted"){
                return $message = "Appointment is Deleted";
            }elseif($type=="NoSlot"){
                return $message = "There is no available slot";
            }elseif($type=="NoQueue"){
                return $message = "This Queue is already allocated";
            }elseif($type=="MoreQueue"){
                return $message = "This Queue number is exceeded";
            }elseif($type=="EmailDuplicate"){
                return $message = "This email already exist";
            }elseif($type=="BlockProfile"){
                return $message = "Your profile has been blocked";
            }elseif($type=="ActiveBooking"){
                return $message = "Sorry we can't put you through this time, there is a scheduled appointment in your list";
            }elseif($type=="OTPError"){
                return $message = "Sorry there is a problem for sending OTP challenge";
            }elseif($type=="PromoError"){
                return $message = "Sorry there is no active promotion on your code";
            }elseif($type=="NoDoctor"){
                return $message = "No Doctors found on this procedure";
            }elseif($type=="NoProcedure"){
                return $message = "No Procedures found under this Doctor";
            }elseif($type=="OpenBooking"){
                return $message = "We are sorry, but you are unable to make multiple appointments";
            }



	}

        public static function getDoctorSlotDetails($slotid,$valDate){
            //echo $valDate;
            return function($item) use ($slotid,$valDate) {
            //$nwval = $item['SlotID'] == $slotid;
            $nwval = ($item['SlotID'] == $slotid && $item['Date'] == $valDate);
            return $nwval;
            };
        }

        public static function getMySlotValues($slotdetails,$val,$active,$valDate){
            if(is_array($slotdetails) && count($slotdetails)>0){
                $output = array_filter($slotdetails, StringHelper::getDoctorSlotDetails($val,$valDate));
                if(is_array($output) && count($output) >0){
                    $newval = array_values($output);
                    if($newval[0]['SlotDetailID'] =="" || $newval[0]['SlotDetailID']==null){
                        return null;
                    }else{
                        if($active==1){
                            return $newval[0]['Active'];
                        }else{

                            return $newval[0]['SlotDetailID'];
                        }
                    }
                }else{
                    return null;
                }
            }
        }

        /* Use      :   Used to find age from date of birth
         *
         *
         */
        public static function findAge($Date){
            $date = new DateTime($Date);
            $now = new DateTime();
            $interval = $now->diff($date);
            return $interval->y;
        }

        /* Use          :   Used to manage Medicloud session
         * User group   :   User, Clinic and Doctor
         *
         */
        public static function getAuthSession(){
            $value = Session::get('user-session');
            //$findUser = App_AuthController::getUserDetails($value);
            $findUser = Auth_Library::ValidateLoginSession($value);
            return $findUser;
        }

        public static function getJwtHrToken($token)
        {   
            $secret = Config::get('config.secret_key');
            $value = JWT::decode($token, $secret);

            if($value) {
                $result = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $value->hr_dashboard_id)->first();
                if($result) {
                    return $result;
                } else {
                    return FALSE;
                }
            }

            return FALSE;
        }

        public static function getJwtHrSession()
        {   
            $secret = Config::get('config.secret_key');
            $token = StringHelper::getToken();
            $result = FALSE;
            try {
                $result = JWT::decode($token, $secret);
            } catch(Exception $e) {
                return FALSE;
            }

            if($result && isset($result->hr_dashboard_id)) {
                $hr = DB::table('customer_hr_dashboard')
                            ->where('hr_dashboard_id', $result->hr_dashboard_id)
                            ->first();
                if($hr) {
                    // change logic
                    if((int)$hr->is_account_linked == 1) {
                        $hr->signed_in = $result->signed_in;
                        $hr->company_linked = $result->company_linked;
                        $hr->under_customer_id = $result->under_customer_id;
                        $hr->hr_activated = 1;
                        $hr->active = 1;
                        $hr->hr_activated = true;
                        if(isset($result->expire_in)) {
                            $hr->expire_in = $result->expire_in;
                        } else {
                            $hr->expire_in = null;
                        }
                    } else {
                        if((int)$hr->active == 1) {
                            $hr->signed_in = $result->signed_in;
                            if(isset($result->expire_in)) {
                                $hr->expire_in = $result->expire_in;
                            } else {
                                $hr->expire_in = null;
                            }
                        } else if((int)$hr->hr_activated == 0) {
                            $hr->status = false;
                            $hr->hr_activated = false;
                        }
                    }                   
                    return $hr;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }

        public static function getJwtEmployeeSession()
        {   
            $secret = Config::get('config.secret_key');
            $token = StringHelper::getToken();
            $result = FALSE;
            try {
                $result = JWT::decode($token, $secret);
            } catch(Exception $e) {
                return FALSE;
            }

            if($result && isset($result->UserID)) {
                $employee = DB::table('user')->where('UserID', $result->UserID)->first();
                if($employee) {
                    $employee->signed_in = $result->signed_in;
                    if(isset($result->expire_in)) {
                        $employee->expire_in = $result->expire_in;
                    } else {
                        $employee->expire_in = null;
                    }
                    return $employee;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }

        public static function getToken( )
        {
            $getRequestHeader = getallheaders();
            if(!empty($getRequestHeader['authorization']) && $getRequestHeader['authorization'] != null) {
                $getRequestHeader['Authorization'] = $getRequestHeader['authorization'];
            }
 
            if(isset($getRequestHeader['Authorization'])) {
                return $getRequestHeader['Authorization'];
            } else {
                return self::requestHeader();
            }
        }

        public static function getHrSession( )
        {
            $value = Session::get('hr-session');
            $result = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $value->hr_dashboard_id)->first();
            if($result) {
                return $result;
            } else {
                return FALSE;
            }
        }

        public static function getEmployeeSession( )
        {

            $secret = Config::get('config.secret_key');
            $token = self::getToken();
            $result = FALSE;
            try {
                $result = JWT::decode($token, $secret);
                if(!$result) {
                    return false;
                }
            } catch(Exception $e) {
                return FALSE;
            }

            $member = DB::table('user')->where('UserID', $result->UserID)->first();
            if($member) {
                if(isset($result->admin_id)) {
                    $member->admin_id = $result->admin_id;
                }
                return $member;
            } else {
                return FALSE;
            }
        }

        /* Use          :   Used to manage Medicloud session
         * User group   :   User, Clinic and Doctor
         *
         */
        public static function getMainSession($getGroup){
            $value = Session::get('user-session');
            //$findUser = App_AuthController::getUserDetails($value);
            $findUser = Auth_Library::ValidateLoginSession($value);

            if($findUser){
                return $findUser;

            /*if(count($findUser) >0){
                if($getGroup == 2){
                    return StringHelper::doctorSession($findUser);
                }elseif($getGroup == 3){
                    return StringHelper::clinicSession($findUser);
                } */
            }else{
                Session::forget('user-session');
                return FALSE;
            }
        }

        //Private function only accessible by this class
        private function userSession(){
            // need to implement later for user
        }
        //Private function only accessible by this class
        private static function doctorSession($findUser){
            if(count($findUser)> 0 && $findUser->UserType == 2 && ($findUser->Ref_ID != null || $findUser->Ref_ID != "")){
                return $findUser;
            }else{
                Session::forget('user-session');
                return FALSE;
            }
        }
        //Private function only accessible by this class
        private static function clinicSession($findUser){
            if(count($findUser)> 0 && $findUser->UserType == 3 && ($findUser->Ref_ID != null || $findUser->Ref_ID != "")){
                return $findUser;
            }else{
                Session::forget('user-session');
                return FALSE;
            }
        }
        /* Use          :   Used to find current time
         *
         */
        public static function CurrentTime(){
            //$dateTime = new DateTime('now', new DateTimeZone('Asia/Singapore'));
            $dateTime = self::TimeZone();
            $timezone = $dateTime->format('H.i');
            return $timezone;
        }
        /* Use          :   Use to find current time with AM/PM
         *
         */
        public static function CurrentTimeSetup($currentdate){
            //$dateTime = new DateTime('now', new DateTimeZone('Asia/Singapore'));
            $dateTime = self::TimeZone();
            if(ArrayHelper::ActivePlusDate($currentdate)==1){
                $timezone = 0;
            }else{
                $timezone = $dateTime->format('H.iA');
            }
            return $timezone;
        }

        /* Use          :   Used to process time zone
         *
         */
        public static function TimeZone(){
            $dateTime = new DateTime('now', new DateTimeZone('Asia/Singapore'));
            return $dateTime;
        }


        public static function OTPChallenge(){
            $otpChallenge = mt_rand(100000, 999999);
            return $otpChallenge;
        }

        public static function twilioConfigs( )
        {
            $config = array();
            $config['sid'] = 'AC1f79827f4e92575fed4ad9562423ca5a';
            $config['token'] = '2a769e9f78b36d59cae26003067071c3';
            // $config['from'] = '+18653200485';
            $config['from'] = 'Mednefits';
            return $config;
        }

        public static function SendOTPSMS($phone, $message){
            $config = self::twilioConfigs();
            $client = new Client($config['sid'], $config['token']);
            // $twilio = new \Aloha\Twilio\Twilio($config['sid'], $config['token'], $config['from']);
            // try {
            //     $twilio->message($phone, $message);
            //     return "TRUE";
            // } catch ( \Services_Twilio_RestException $e ) {
            //     // return 'FALSE';
            //     return self::sendSmsUnsupported($phone, $message);
            // }
            if(strrpos($phone, '+65') !== false) {
                $from = $config['from'];
                // return TRUE;
            } else {
                $from = '+18653200485';
                // return FALSE;
            }

            return $client->messages->create(
                $phone,
                array(
                    'from' => $from,
                    'body' => $message,
                )
            );
        }

        public static function sendSmsUnsupported($phone, $message) {
            $config = self::twilioConfigs();
            $client = new Client($config['sid'], $config['token']);
            // $return = $client->messages->create(
            //     // the number you'd like to send the message to
            //     $phone,
            //     array(
            //         // A Twilio phone number you purchased at twilio.com/console
            //         'from' => '+18653200485',
            //         // the body of the text message you'd like to send
            //         'body' => $message
            //     )
            // );

            // if($return->accountSid) {
            //     return 'TRUE';
            // } else {
            //     return 'FALSE';
            // }
            // $twilio = new \Aloha\Twilio\Twilio($config['sid'], $config['token'], '+18653200485');
            // try {
            //     $twilio->message($phone, $message);
            //     return "TRUE";
            // } catch ( \Services_Twilio_RestException $e ) {
            //     return 'FALSE';
            //     // return $e;
            // }
            if(strrpos($phone, '+65') !== false) {
                $from = $config['from'];
                // return TRUE;
            } else {
                $from = '+18653200485';
                // return FALSE;
            }

            return $client->messages->create(
                $phone,
                array(
                    'from' => $from,
                    'body' => $message,
                )
            );
        }

        public static function testSMS($phone, $message)
        {

            $config = self::twilioConfigs();
            $twilio = new \Aloha\Twilio\Twilio($config['sid'], $config['token'], $config['from']);
            try {
                return $twilio->message($phone, $message);
            } catch ( \Services_Twilio_RestException $e ) {
                return 'FALSE';
            }
        }

        public static function TestSendOTPSMS($phone, $message){
            // $config = \SmsHelper::commzGateConfigs();
            $config = self::twilioConfigs();
            $client = new Client($config['sid'], $config['token']);
            $new_message = $message.' is your Mednefits verification code.';
            // $return = $client->messages->create(
            //     // the number you'd like to send the message to
            //     $phone,
            //     array(
            //         // A Twilio phone number you purchased at twilio.com/console
            //         'from' => $config['from'],
            //         // the body of the text message you'd like to send
            //         'body' => $new_message
            //     )
            // );
            // if($return) {
            //     return "TRUE";
            // } else {
            //     return self::sendSmsUnsupported($phone, $new_message);
            // }
            // return var_dump($return);
            // if($return->accountSid) {
            //     return 'TRUE';
            // } else {
            //     return self::sendSmsUnsupported($phone, $new_message);
            // }
            // $twilio = new \Aloha\Twilio\Twilio($config['sid'], $config['token'], $config['from']);
            // try {
            //     $twilio->message($phone, $new_message);
            //     return "TRUE";
            // } catch ( \Services_Twilio_RestException $e ) {
            //     // return 'FALSE';
            //     return self::sendSmsUnsupported($phone, $new_message);
            // }
            if(strrpos($phone, '+65') !== false) {
                $from = $config['from'];
                // return TRUE;
            } else {
                $from = '+18653200485';
                // return FALSE;
            }

            return $client->messages->create(
                $phone,
                array(
                    'from' => $from,
                    'body' => $new_message,
                )
            );
            // $mobile = preg_replace('/\s+/', '', $phone);
            // $data_message = array(
            //     'ID'        => $config['id'],
            //     'Password'  => $config['password'],
            //     'Mobile'    => $mobile,
            //     'Message'   => $new_message,
            //     'Type'      => 'A',
            //     'Sender'    => $config['from']
            // );

            // $fields_string = http_build_query($data_message);
            // $url = "https://www.commzgate.net/gateway/SendMsg?".$fields_string;
            // $curl = curl_init();
            // curl_setopt($curl, CURLOPT_URL, $url);
            // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // $resp = curl_exec($curl);
            // curl_close($curl);
            // return $resp;
        }


        public static function GetWeekFromTime(){
            self::Set_Default_Timezone();
            $currentWeek = date("w", time());
            if($currentWeek==0){
                $nowWeek = "Sun";
            }elseif($currentWeek==1){
                $nowWeek = "Mon";
            }elseif($currentWeek==2){
                $nowWeek = "Tue";
            }elseif($currentWeek==3){
                $nowWeek = "Wed";
            }elseif($currentWeek==4){
                $nowWeek = "Thu";
            }elseif($currentWeek==5){
                $nowWeek = "Fri";
            }elseif($currentWeek==6){
                $nowWeek = "Sat";
            }else{
                $nowWeek = null;
            }
            return $nowWeek;
        }

        public static function Set_Default_Timezone(){
            date_default_timezone_set("Asia/Singapore");
        }


        public static function GetClinicOpenTimes($clinicid){
            $findClinicTimes = ClinicLibrary::FindClinicTimes($clinicid);
            if($findClinicTimes){
                //$weeks = array();
                foreach($findClinicTimes as $ctvalue){
                    $weeks = self::GetOpenWeeks($ctvalue);
                    $cltime['timeid'] = $ctvalue->ClinicTimeID;
                    $cltime['weeks'] =  $weeks;
                    $cltime['starttime'] = $ctvalue->StartTime;
                    $cltime['endtime'] =  $ctvalue->EndTime;
                    $cltimeval[] = $cltime;
                }
            }else{
                $cltimeval = null;
            }
            return $cltimeval;
        }

        public static function GetOpenWeeks($ctvalue){
            $weeks = array();
            if($ctvalue->Mon==1){
                $weeks[] = "Mon";
            }if($ctvalue->Tue==1){
                $weeks[] = "Tue";
            }if($ctvalue->Wed==1){
                $weeks[] = "Wed";
            }if($ctvalue->Thu==1){
                $weeks[] = "Thu";
            }if($ctvalue->Fri==1){
                $weeks[] = "Fri";
            }if($ctvalue->Sat==1){
                $weeks[] = "Sat";
            }if($ctvalue->Sun==1){
                $weeks[] = "Sun";
            }
            $finalValue = implode(', ', $weeks);
            return $finalValue;
        }

        public static function GetClinicOpenStatus($clinicid){
            $findWeek = self::GetWeekFromTime();
            $findClinicTimeStatus = ClinicLibrary::FindClinicTimesStatus($clinicid,$findWeek);
            $openstatus = 0;
            if($findClinicTimeStatus){
                $timecounter = 0;
                foreach($findClinicTimeStatus as $ctstatus){
                    if($timecounter ==0){
                        if(strtotime($ctstatus->StartTime) <= time() && strtotime($ctstatus->EndTime) >=time()){
                            $openstatus = 1;
                            $timecounter = 1;
                        }
                    }
                }
            }
            return $openstatus;
        }

        /* Use      :   Used to find stopping repeat time
         *
         */
        public static function FindStopRepeatDate($currentDate){
            self::Set_Default_Timezone();
            $date_string = $currentDate;
            $date = new DateTime($date_string);

            $weekno = date("w", strtotime($date_string));

            $currentValue = 7 - $weekno;
            if($currentValue != 7){
                $newDate = $date->modify("+".$currentValue." day");
            }else{
                $newDate = date('d-m-Y');
            }
            $finalDate = $date->format("d-m-Y");

            return $finalDate;
        }



        public static function FindWeekFromDate($currentDate){
            self::Set_Default_Timezone();
            $weekno = date("w", strtotime($currentDate));
            $findWeek = '';
            if($weekno==0){
                $findWeek = "Sun";

            }elseif($weekno==1){
                $findWeek = "Mon";
            }if($weekno==2){
                $findWeek = "Tue";
            }if($weekno==3){
                $findWeek = "Wed";
            }if($weekno==4){
                $findWeek = "Thu";
            }if($weekno==5){
                $findWeek = "Fri";
            }if($weekno==6){
                $findWeek = "Sat";
            }
            return $findWeek;
        }

        public static function Deployment(){
            $config = Config::get('config.deployment');
            if($config == "Production"){
                return 1;
            }elseif($config == "Development") {
                return 2;
            }else{
                return 0;
            }
        }



// nhr 2016-4-1
public static function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = //chr(123)// "{"
             substr($charid, 0, 8)//.$hyphen
            .substr($charid,12, 4)///.$hyphen
            .substr($charid,16, 4)//.$hyphen
            .substr($charid,20,12);
            //.chr(125);// "}"
        return $uuid;
    }
}

// nhr 2016-7-22 get random pw


public static function get_random_password($length)
{

        // $characters = '0123456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ';
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
}

    public static function getMedisysSessionToken( )
    {
        if(!empty(Session::get('medisys_token'))) {
            return Session::get('medisys_token');
        } else {
            $token = MediSys_Library::getMedisysToken();
            Session::put('medisys_token', $token);
            return $token;
        }
    }

    public static function saveSMSMLogs($clinic_id, $name, $code, $phone, $smsMessage)
    {

        $data['name']           = $name;
        $data['message']        = $smsMessage;
        $data['phone_code']     = $code;
        $data['phone_number']   = $phone;
        $data['clinic_id']      = $clinic_id;

        $sms = new SmsHistory();
        $sms->insert($data);
    }

    public static function sendPost($url, $data, $headers)
    {
        $fields_string = http_build_query($data);
        $curl = curl_init();
        if($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_POST, count($data));
            curl_setopt($curl,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        $json = json_decode($resp, true);
        curl_close($curl);
        return $json;
    }

    public static function BankList( )
    {
        return ['DBS/POSB BANK', 'OCBC', 'UOB', 'CITI BANK', 'MAYBANK', 'STANDARD CHARTERED BANK', 'HSBC', 'BANK OF CHINA', 'RHB BANK', 'CIMB BANK BERHAD'];
    }

    public static function checkUserType($id)
    {
        $result = DB::table('user')->where('UserID', $id)->first();
        if($result) {
            return array('user_type' => $result->UserType, 'access_type' => $result->access_type);
        } else {
            return false;
        }
        
    }

    public static function getUserId($id)
    {
        $result = DB::table('user')->where('UserID', $id)->first();
        if((int)$result->UserType == 5 && (int)$result->access_type == 0 || (int)$result->UserType == 5 && (int)$result->access_type == 1)
        {
            $user_id = $id;
        } else if((int)$result->UserType == 5 && (int)$result->access_type == 2 || (int)$result->UserType == 5 && (int)$result->access_type == 3)
        {
            $owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $id)->first();
            $user_id = $owner->owner_id;
        } else {
            $user_id = $id;
        }

        return $user_id;
    }

    public static function getOwnerSubAccountsID($id, $user_type, $access_type)
    {
        $ids = [];
        $ids[] = $id;

        if($user_type == 5 && $access_type == 0 || $user_type == "5" && $access_type == "0") {
            $results = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $id)->get();
        } else if($user_type == 5 && $access_type == 2 || $user_type == "5" && $access_type == "2" || $user_type == 5 && $access_type == 3 || $user_type == "5" && $access_type == "3") {
            // get owner
            $owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $id)->first();
            $results = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $owner->owner_id)->get();
            $ids[] = $owner->owner_id;
        }

        foreach ($results as $key => $res) {
            if($res->user_id != $id) {
                array_push($ids, $res->user_id);
            }
        }

        return $ids;
    }

    public static function getSubAccountsID($id)
    {

        $user = DB::table('user')->where('UserID', $id)->first();
        // return $user->UserType;
        $ids = [];
        $ids[] = (int)$id;

        if($user) {
            if($user->UserType == 5 && $user->access_type == 0 || $user->UserType == "5" && $user->access_type == "0") {
                $results = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $id)->get();
                foreach ($results as $key => $res) {
                    if($res->user_id != $id) {
                        array_push($ids, $res->user_id);
                    }
                }
            } else if($user->UserType == 5 && $user->access_type == 2 || $user->UserType == "5" && $user->access_type == "2" || $user->UserType == 5 && $user->access_type == 3 || $user->UserType == "5" && $user->access_type == "3") {
                // get owner
                $owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $id)->first();
                $results = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $owner->owner_id)->get();
                $ids[] = $owner->owner_id;
                foreach ($results as $key => $res) {
                    if($res->user_id != $id) {
                        array_push($ids, $res->user_id);
                    }
                }
            }
        }


        return $ids;
    }

    public static function getCustomerId($id)
    {
        $user = DB::table('user')->where('UserID', $id)->first();

        if($user) {
            $corporate_member = DB::table('corporate_members')->where('user_id', $id)->first();
            if($corporate_member) {
                $corporate = DB::table('corporate')->where('corporate_id', $corporate_member->corporate_id)->first();
                if($corporate) {
                    $account = DB::table('customer_link_customer_buy')->where('corporate_id', $corporate->corporate_id)->first();
                    if($account) {
                        return $account->customer_buy_start_id;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public static function checkEligibleFeature($id)
    {
        $user = DB::table('user')->where('UserID', $id)->first();
        if($user) {
            if($user->UserType == 5 && $user->access_type == 0 || $user->UserType == "5" && $user->access_type == "0") {
                return TRUE;
            } else if($user->UserType == 5 && $user->access_type == 2 || $user->UserType == "5" && $user->access_type == "2" || $user->UserType == 5 && $user->access_type == 3 || $user->UserType == "5" && $user->access_type == "3") {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public static function checkEmployeeEligibleFeature($id)
    {
        $user = DB::table('user')->where('UserID', $id)->first();

        if($user) {

            $corporate_member = DB::table('corporate_members')->where('user_id', $id)->where('removed_status', 0)->first( );

            if($corporate_member) {
                $account_link = DB::table('customer_link_customer_buy')->where('corporate_id', $corporate_member->corporate_id)->first();

                if($account_link) {
                    $hr_account = DB::table('customer_hr_dashboard')->where('customer_buy_start_id', $account_link->customer_buy_start_id)->first();

                    if($hr_account) {
                        if($hr_account->qr_payment == 1 && $hr_account->wallet == 1 || $hr_account->qr_payment == "1" && $hr_account->wallet == "1") {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }

        } else {
            return FALSE;
        }
    }

    public static function getMYRSGD()
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://free.currencyconverterapi.com/api/v6/convert?q=SGD_MYR&compact=y");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($curl);
            $json = json_decode($resp, true);
            curl_close($curl);
            return $json["SGD_MYR"]["val"];

        } catch(Exception $e) {
            return false;
        }
    }

    public static function newLitePlanStatus($id)
    {

        // check if employee or dependent
        $type = self::checkUserType($id);
        $dependent = false;
        if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
        {

          $customer_id = self::getCustomerId($id);

          if(!$customer_id) {
            return FALSE;
          }
          
          $plan = DB::table('customer_plan')
                    ->where('customer_buy_start_id', $customer_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
          if($plan->account_type == "lite_plan") {
              return TRUE;
          } else if($plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite" || $plan->account_type == "trial_plan" && $plan->secondary_account_type == "trial_plan_lite" || $plan->account_type == "super_pro_plan") {
              return TRUE;
          } else {
              return FALSE;
          }
        } else {
            $employee_id = self::getUserId($id);
            $customer_id = self::getCustomerId($employee_id);

            if(!$customer_id) {
                return FALSE;
            }
          
            $plan = DB::table('customer_plan')
                    ->where('customer_buy_start_id', $customer_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            // get dependent history
            $dependent_history = DB::table('dependent_plan_history')
                                    ->where('user_id', $id)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
            $dependent_plan = DB::table('dependent_plans')
                                ->where('dependent_plan_id', $dependent_history->dependent_plan_id)
                                ->first();
            if($dependent_plan->account_type == "lite_plan") {
                return TRUE;
            } else if($dependent_plan->account_type == "insurance_bundle" && $dependent_plan->secondary_account_type == "insurance_bundle_lite" || $dependent_plan->account_type == "trial_plan" && $dependent_plan->secondary_account_type == "trial_plan_lite" || $plan->account_type == "super_pro_plan") {
              return TRUE;
            } else {
              return FALSE;
            }
        }
    }

    public static function litePlanStatus($id)
    {
        $customer_id = self::getCustomerId($id);

        if(!$customer_id) {
            return FALSE;
        }

        $plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

        if($plan->account_type === "lite_plan") {
            return TRUE;
        } else if($plan->account_type === "insurance_bundle" && $plan->secondary_account_type === "insurance_bundle_lite") {
            return TRUE;
        } else if($plan->account_type === "trial_plan" && $plan->secondary_account_type === "trial_plan_lite"){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function liteCompanyPlanStatus($customer_id)
    {
        $plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

        if($plan->account_type === "lite_plan" || $plan->account_type === "insurance_bundle" && $plan->secondary_account_type === "insurance_bundle_lite" || $plan->account_type === "trial_plan" && $plan->secondary_account_type === "trial_plan_lite" || $plan->account_type === "super_pro_plan") {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function customLoginToken($data)
    {   

        $returnObject = new stdClass();

        if(empty($data['grant_type'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"grant_type\" parameter.";
            return $returnObject;
        } else if(empty($data['client_secret'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"client_secret\" parameter.";
            return $returnObject;
        } else if(empty($data['username'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"username\" parameter.";
            return $returnObject;
        } else if(empty($data['password'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"password\" parameter.";
            return $returnObject;
        } else if(empty($data['client_id'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"client_id\" parameter.";
            return $returnObject;
        } else {
            // check creds
            $user = new User();
            $result = $user->authLogin($data['username'], $data['password']);

            if($result) {
                $session_data = array(
                    'client_id'             => $data['client_id'],
                    'owner_type'            => 'user',
                    'owner_id'              => $result,
                    'client_redirect_uri'   => NULL
                );

                $session_class = new OauthSessions( );
                $session = $session_class->createSession($session_data);

                if($session) {
                    $token_data = array(
                        'id'        => self::getAlgorithm()->generate(40),
                        'session_id'  => $session->id,
                        'expire_time' => time() + 72000
                    );

                    $token_class = new OauthAccessTokens( );
                    $token = $token_class->createToken($token_data);
                    $get_token = DB::table('oauth_access_tokens')->where('session_id', $token->session_id)->orderBy('created_at', 'desc')->first();

                    if($get_token) {
                        $returnObject->error = "false";
                        $returnObject->status = TRUE;
                        $returnObject->data['access_token'] = $get_token->id;
                        $returnObject->data['token_type'] = 'Bearer';
                        $returnObject->data['expires_in'] = 7200;
                        $returnObject->data['pin_setup'] = FALSE;
                        $returnObject->fields = TRUE;
                        return $returnObject;
                        // return array('status' => TRUE, 'access_token' => $get_token->id);
                    } else {
                        $returnObject->status = FALSE;
                        $returnObject->error = 'invalid_credentials';
                        $returnObject->error_description = 'The user credentials were incorrect.';
                        $returnObject->fields = TRUE;
                        return $returnObject;
                    }
                } else {
                    $returnObject->status = FALSE;
                    $returnObject->error = 'invalid_credentials';
                    $returnObject->error_description = 'The user credentials were incorrect.';
                    $returnObject->fields = TRUE;
                    return $returnObject;

                }
            } else {
                $returnObject->status = FALSE;
                $returnObject->error = 'invalid_credentials';
                $returnObject->error_description = 'The user credentials were incorrect.';
                $returnObject->fields = TRUE;
                return $returnObject;
            }
        }
    }

    public static function newCustomLoginToken($data)
    {   

        $returnObject = new stdClass();

        if(empty($data['grant_type'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"grant_type\" parameter.";
            return $returnObject;
        } else if(empty($data['client_secret'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"client_secret\" parameter.";
            return $returnObject;
        } else if(empty($data['username'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"username\" parameter.";
            return $returnObject;
        } else if(empty($data['password'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"password\" parameter.";
            return $returnObject;
        } else if(empty($data['client_id'])) {
            $returnObject->status = FALSE;
            $returnObject->fields = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"client_id\" parameter.";
            return $returnObject;
        } else {
            // check creds
            $user = new User();
            $result = $user->newAuthLogin($data['username'], $data['password']);
            if($result) {
                $session_data = array(
                    'client_id'             => $data['client_id'],
                    'owner_type'            => 'user',
                    'owner_id'              => $result,
                    'client_redirect_uri'   => NULL
                );

                $session_class = new OauthSessions( );
                $session = $session_class->createSession($session_data);

                if($session) {
                    $token_data = array(
                        'id'        => self::getAlgorithm()->generate(40),
                        'session_id'  => $session->id,
                        'expire_time' => time() + 72000
                    );

                    $token_class = new OauthAccessTokens( );
                    $token = $token_class->createToken($token_data);
                    $get_token = DB::table('oauth_access_tokens')->where('session_id', $token->session_id)->orderBy('created_at', 'desc')->first();

                    if($get_token) {
                        $returnObject->error = "false";
                        $returnObject->status = TRUE;
                        $returnObject->data['access_token'] = $get_token->id;
                        $returnObject->data['token_type'] = 'Bearer';
                        $returnObject->data['expires_in'] = 7200;
                        $returnObject->data['pin_setup'] = FALSE;
                        $returnObject->fields = TRUE;
                        return $returnObject;
                        // return array('status' => TRUE, 'access_token' => $get_token->id);
                    } else {
                        $returnObject->status = FALSE;
                        $returnObject->error = 'invalid_credentials';
                        $returnObject->error_description = 'The user credentials were incorrect.';
                        $returnObject->fields = TRUE;
                        return $returnObject;
                    }
                } else {
                    $returnObject->status = FALSE;
                    $returnObject->error = 'invalid_credentials';
                    $returnObject->error_description = 'The user credentials were incorrect.';
                    $returnObject->fields = TRUE;
                    return $returnObject;

                }
            } else {
                $returnObject->status = FALSE;
                $returnObject->error = 'invalid_credentials';
                $returnObject->error_description = 'The user credentials were incorrect.';
                $returnObject->fields = TRUE;
                return $returnObject;
            }
        }
    }

    public static function createLoginToken($user_d, $client_id)
    {
        $returnObject = new stdClass();
        $session_data = array(
            'client_id'             => $client_id,
            'owner_type'            => 'user',
            'owner_id'              => $user_d,
            'client_redirect_uri'   => NULL
        );

        $session_class = new OauthSessions( );
        $session = $session_class->createSession($session_data);

        if($session) {
            $token_data = array(
                'id'        => self::getAlgorithm()->generate(40),
                'session_id'  => $session->id,
                'expire_time' => time() + 72000
            );

            $token_class = new OauthAccessTokens( );
            $token = $token_class->createToken($token_data);
            $get_token = DB::table('oauth_access_tokens')->where('session_id', $token->session_id)->orderBy('created_at', 'desc')->first();

            if($get_token) {
                $returnObject->error = "false";
                $returnObject->status = TRUE;
                $returnObject->data['access_token'] = $get_token->id;
                $returnObject->data['token_type'] = 'Bearer';
                $returnObject->data['expires_in'] = 7200;
                $returnObject->data['pin_setup'] = FALSE;
                $returnObject->fields = TRUE;
                return $returnObject;
            } else {
                $returnObject->status = FALSE;
                $returnObject->error = 'invalid_credentials';
                $returnObject->error_description = 'The user credentials were incorrect.';
                $returnObject->fields = TRUE;
                return $returnObject;
            }
        } else {
            $returnObject->status = FALSE;
            $returnObject->error = 'invalid_credentials';
            $returnObject->error_description = 'The user credentials were incorrect.';
            $returnObject->fields = TRUE;
            return $returnObject;
        }
    }

    public static function getAlgorithm()
    {
        if (is_null(self::$algorithm)) {
            self::$algorithm = new DefaultAlgorithm();
        }

        return self::$algorithm;
    }

    public static function socketConnection($clinic_id, $user_id)
    {
        
        $config = Config::get('config.deployment');
        $dev = "";

        if($config == "Production"){
          $dev = "production";
        }elseif($config == "Development") {
          $dev = "development";
        }else{
          $dev = "local";
        }

        return "claim-notification-event_clinic_".$dev."_".$clinic_id."_".$user_id;
    }

    public static function socketConnectionCheckIn($clinic_id, $user_id)
    {
        
        $config = Config::get('config.deployment');
        $dev = "";

        if($config == "Production"){
          $dev = "production";
        }elseif($config == "Development") {
          $dev = "development";
        }else{
          $dev = "local";
        }

        return "check-in-notification-event_clinic_".$dev."_".$clinic_id."_".$user_id;
    }

    public static function socketConnectionCheckInRemove($clinic_id, $user_id)
    {
        
        $config = Config::get('config.deployment');
        $dev = "";

        if($config == "Production"){
          $dev = "production";
        }elseif($config == "Development") {
          $dev = "development";
        }else{
          $dev = "local";
        }

        return "check-in-remove-notification-event_clinic_".$dev."_".$clinic_id."_".$user_id;
    }

     public static function validIdentification($number)
    {
        return true;
        // if (strlen($number) !== 9) {
        //     return false;
        // }
        // $newNumber = strtoupper($number);
        // $icArray = [];
        // for ($i = 0; $i < 9; $i++) {
        //     $icArray[$i] = $newNumber{$i};
        // }
        // $icArray[1] = intval($icArray[1], 10) * 2;
        // $icArray[2] = intval($icArray[2], 10) * 7;
        // $icArray[3] = intval($icArray[3], 10) * 6;
        // $icArray[4] = intval($icArray[4], 10) * 5;
        // $icArray[5] = intval($icArray[5], 10) * 4;
        // $icArray[6] = intval($icArray[6], 10) * 3;
        // $icArray[7] = intval($icArray[7], 10) * 2;

        // $weight = 0;
        // for ($i = 1; $i < 8; $i++) {
        //     $weight += $icArray[$i];
        // }
        // $offset = ($icArray[0] === "T" || $icArray[0] == "G") ? 4 : 0;
        // $temp = ($offset + $weight) % 11;

        // $st = ["J", "Z", "I", "H", "G", "F", "E", "D", "C", "B", "A"];
        // $fg = ["X", "W", "U", "T", "R", "Q", "P", "N", "M", "L", "K"];

        // $theAlpha = "";
        // if ($icArray[0] == "S" || $icArray[0] == "T") {
        //     $theAlpha = $st[$temp];
        // } else if ($icArray[0] == "F" || $icArray[0] == "G") {
        //     $theAlpha = $fg[$temp];
        // }
        // return ($icArray[8] === $theAlpha);
    }

    public static function checkToken($token)
    {
        $result = self::getJwtHrToken($token);
        if(!$result) {
            return array(
                'status'    => FALSE,
                'message'   => 'Need to authenticate user.'
            );
        }
        return $result;
    }

    public static function validateDate($date)
		{
			return (bool)strtotime($date);
		}

		public static function is_Date($str){ 
      $str = str_replace('/', '-', $str);
      return is_numeric(strtotime($str));
    }

    public static function floorp($val, $precision)
    {
        $mult = pow(10, $precision);       
        return floor($val * $mult) / $mult;
    }

    public static function removeRows($data)
    {
        foreach($data as $key => $row) {
            $row = array_filter($row,
                                function($cell) {
                                    return !is_null($cell);
                                }
                   );
            if (count($row) == 0) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    public static function thousandsCurrencyFormat($num) {

      if($num>1000) {

            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;

      }

      return $num;
    }

    public static function verifyXAccessKey () {
        
        $getRequestHeader = getallheaders();
        $returnObject = new stdClass();
        $todate =  date("Y-m-d H:i:s");

        // Confirmed X-Access Key
        if (
            (!isset($getRequestHeader['X-ACCESS-KEY']) && isset($getRequestHeader['X-MEMBER-ID']))
            ||(!isset($getRequestHeader['x-access-key']) && isset($getRequestHeader['x-member-id']))
        ) {
            $returnObject->error = TRUE;
            $returnObject->message = 'X-ACCESS-KEY not defined.';
            return $returnObject;
        } else if (
            (isset($getRequestHeader['X-ACCESS-KEY']) && !isset($getRequestHeader['X-MEMBER-ID']))
            || (isset($getRequestHeader['x-access-key']) && !isset($getRequestHeader['x-member-id']))
        ) {
            $returnObject->error = TRUE;
            $returnObject->message = 'X-MEMBER-ID not defined.';
            return $returnObject;
        } else if (
            (isset($getRequestHeader['X-ACCESS-KEY']) && isset($getRequestHeader['X-MEMBER-ID']))
            || (isset($getRequestHeader['x-access-key']) && isset($getRequestHeader['x-member-id']))
        ) {

            $xAccessKey = isset($getRequestHeader['X-ACCESS-KEY']) ? $getRequestHeader['X-ACCESS-KEY']: $getRequestHeader['x-access-key'];
            $user_id = isset($getRequestHeader['X-MEMBER-ID'])? $getRequestHeader['X-MEMBER-ID']: $getRequestHeader['x-member-id'];
            /***
             * Description: Refactor algorithm starts here.
             * Date: July 6, 2020
             * Developer: Stephen
            ***/
            
            // check member type
            $member_type = PlanHelper::getUserAccountType($user_id);
            if(!$member_type) {
                $returnObject->error = TRUE;
                $returnObject->message = 'X-MEMBER-ID not member or dependent.';
                return $returnObject;
            }

            $member_id = $user_id;
            if($member_type == "dependent") {
                $owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $user_id)->first();
			    $member_id = $owner->owner_id;
            }

            // Check access key details if existed
            $accessKeyDetails = DB::table('customer_accessKey')
                                    ->join('customer_access_link_company', 'customer_access_link_company.accessKeyId', '=', 'customer_accessKey.accessKeyId')
                                    ->join('customer_link_customer_buy', 'customer_link_customer_buy.customer_buy_start_id', '=', 'customer_access_link_company.customer_id')
                                    ->join('corporate', 'corporate.corporate_id', '=', 'customer_link_customer_buy.corporate_id')
                                    ->join('corporate_members', 'corporate_members.corporate_id', '=', 'corporate.corporate_id')
                                    ->where('customer_accessKey.accessKey', $xAccessKey)
                                    ->where('corporate_members.user_id', $member_id)
                                    ->where('corporate_members.removed_status', 0)
                                    ->where('customer_access_link_company.status',1)
                                    ->first();
            // Check if there are new access Key details
            $newAccessKeyDetails = DB::table('customer_accessKey')
                                    ->join('customer_access_link_company', 'customer_access_link_company.accessKeyId', '=', 'customer_accessKey.accessKeyId')
                                    ->join('customer_link_customer_buy', 'customer_link_customer_buy.customer_buy_start_id', '=', 'customer_access_link_company.customer_id')
                                    ->join('corporate', 'corporate.corporate_id', '=', 'customer_link_customer_buy.corporate_id')
                                    ->join('corporate_members', 'corporate_members.corporate_id', '=', 'corporate.corporate_id')
                                    ->where('corporate_members.user_id', $member_id)
                                    ->where('corporate_members.removed_status', 0)
                                    ->where('customer_access_link_company.status',1)
                                    ->orderBy('customer_accessKey.expiry_date', 'DESC')
                                    ->first();

            if (!$accessKeyDetails) {
                if ($newAccessKeyDetails && $newAccessKeyDetails->accessKey != $xAccessKey) {
                    $returnObject->message = 'Access key already expired. Please reconnect again to get new access keys.';
                } else {
                    $returnObject->message = 'Unathorize Access. Access key/Customer ID does not exist.';
                }
                $returnObject->error = TRUE;
                return $returnObject;
            } else {

                // Check access key expiration
                $accessKeyExpiryDate =  $accessKeyDetails->expiry_date;
               
                if ($todate > $accessKeyExpiryDate) {
                    // Create new access key
                    $newAccessKey = md5($accessKeyDetails->customer_id.$todate);
                    $expiraryDate = date("Y-m-d H:i:s",strtotime('+30 days',strtotime(date("Y-m-d H:i:s"))));
                  
                    $newAccessKeyData =  array(
                        'accessKey' => $newAccessKey, 
                        'expiry_date' => $expiraryDate,
                        'syncEnable' => $accessKeyDetails->syncEnable,
                        'thirdPartyLink' => $accessKeyDetails->thirdPartyLink,
                        'authorizartion' => $accessKeyDetails->authorizartion,
                    );

                     // Check access key for new data if the used accesskey already expired.
                    $checkForNewAccesskey = DB::table('customer_accessKey')
                                            ->where('accessKey', $newAccessKey)
                                            ->where('expiry_date', $expiraryDate)
                                            ->first();
                    
                    if ($checkForNewAccesskey) {
                        $returnObject->error = TRUE;
                        $returnObject->message = 'Access key already expired. Please reconnect again to get new access keys.';
                        return $returnObject;
                    }
                    // Create new Access key
                    $lastInsertedDataId = DB::table('customer_accessKey')
                        ->insertGetId($newAccessKeyData);

                    // Update Access Key company link key ID
                    DB::table('customer_access_link_company')
                        ->where('accessKeyId', $accessKeyDetails->accessKeyId)
                        ->update(['accessKeyId' =>  $lastInsertedDataId]);
                    // Send access to the third party database.

                    // Return message to refresh key
                    $returnObject->error = TRUE;
                    $returnObject->message = 'Access key already expired. Please reconnect again to get new access key.';
                    return $returnObject;
                } else {
                    
                    // Check if there are existing session
                    $sessionHistory = DB::table('oauth_sessions')
                    ->where("owner_id", $user_id)
                    ->orderBy("created_at", "desc")
                    ->first();
                    
                    if (count((array)$sessionHistory) > 0) {
                        // check session expiration
                        $tokenDetails = DB::table('oauth_access_tokens')
                        ->where("session_id", $sessionHistory->id)
                        ->first();
                        
                        // Convert time to datetime format
                        $tokenExpirationDateTime = date("Y-m-d H:i:s", $tokenDetails->expire_time);
                       
                        if ($tokenExpirationDateTime == $todate) {
                            $authorization = "Authorization: ".self::getAlgorithm()->generate(40);
                            header($authorization);
                            // Create oauth session
                            $oauth = array(
                                "client_id" => "cfcd208495d565ef66e7dff9f98764da",
                                "owner_type" => "user",
                                "owner_id" => $user_id,
                                "client_redirect_uri" => null,
                                "created_at" => $todate,
                                "updated_at" => $todate
                            );
                            DB::table('oauth_sessions')->insert($oauth);
                            $session_id = DB::getPdo()->lastInsertId();
                            
                            // Create oauth session token
                            $oauthToken = array(
                                "id" => self::getAlgorithm()->generate(40),
                                "session_id" => $session_id,
                                "expire_time" => time() + 72000,
                                "created_at" => $todate,
                                "updated_at" => $todate
                            );
                            DB::table('oauth_access_tokens')->insert($oauthToken);   
                            return self::getAlgorithm()->generate(40);
                        } else {
                            $authorization = "Authorization: ".$tokenDetails->id;
                            header($authorization);
                            return $tokenDetails->id;
                        }
                    } else {
                        $authorization = "Authorization: ".self::getAlgorithm()->generate(40);
                        header($authorization);
                        // Create oauth session
                        $oauth = array(
                            "client_id" => "cfcd208495d565ef66e7dff9f98764da",
                            "owner_type" => "user",
                            "owner_id" => $user_id,
                            "client_redirect_uri" => null,
                            "created_at" => $todate,
                            "updated_at" => $todate
                        );
                        DB::table('oauth_sessions')->insert($oauth);
                        $session_id = DB::getPdo()->lastInsertId();
                        
                        // Create oauth session token
                        $oauthToken = array(
                            "id" => self::getAlgorithm()->generate(40),
                            "session_id" => $session_id,
                            "expire_time" => time() + 72000,
                            "created_at" => $todate,
                            "updated_at" => $todate
                        );
                        
                        DB::table('oauth_access_tokens')->insert($oauthToken); 
                        return $oauthToken['id'];
                    }
                }
            }

            /***Refactor algorithm end here.***/
            
        }
        
    }
    
    public static function validateFormatDate($date, $firstFormat, $secondFormat)
	{
		$d = DateTime::createFromFormat($firstFormat, $date);
		$c = DateTime::createFromFormat($secondFormat, $date);
		return $d && $d->format($firstFormat) === $date || $c && $c->format($secondFormat) === $date;
		// return ['format' => $d->format($format), 'date' => $date];
	}
}
