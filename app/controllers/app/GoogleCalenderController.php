<?php
use Illuminate\Support\Facades\Input;

class GoogleCalenderController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	
// nhr 2016-1-27
	public function getconfig()
	{
		// define('SCOPES', implode(' ', array(
		//   Google_Service_Calendar::CALENDAR,'https://www.googleapis.com/auth/userinfo.email')
		// ));
		$hostName = $_SERVER['HTTP_HOST'];
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  	$server = $protocol.$hostName;
		
		$google_client = new Google_Client();
		$google_client->setApplicationName('Medicloud');
		$google_client->setRedirectUri($server.'/app/gcal/getClientToken');
		// $google_client->setRedirectUri('http://localhost/medicloud_v003/public/app/gcal/getClientToken');
		// $google_client->setRedirectUri('http://ec2-54-255-185-218.ap-southeast-1.compute.amazonaws.com/nuclei_mc_r1/public/app/gcal/getClientToken');
		// $google_client->setClientId('435398632542-oe70nsqe1mtpd5j1gteeb4anp4v5p315.apps.googleusercontent.com');
		// $google_client->setClientSecret('KZFY_DK7i21cBJuVyiGf8Of9');
		$google_client->setClientId('186173794919-hm0ejste4uocpnsg9p8hjd5ea3201ss8.apps.googleusercontent.com');
		$google_client->setClientSecret('FBBhPQ9SeHdEwUPTLKAuyn-5');
		// $google_client->setScopes(SCOPES);
		

		return $google_client;
	}


	public function sendOAuthRequest()
	{	
		
		define('SCOPES', implode(' ', array(
		  Google_Service_Calendar::CALENDAR,'https://www.googleapis.com/auth/userinfo.email')
		));

		$google_client = $this->getconfig();
		$google_client->setScopes(SCOPES);
		$google_client->setAccessType('offline');
		$google_client->setApprovalPrompt('force');
		$authUrl = $google_client->createAuthUrl();
		
		$doctorid = $_POST['doctorid'];
		$gmail 	  = $_POST['gmail'];
		$google_link_code = StringHelper::get_random_password(12).$doctorid;

		if(StringHelper::Deployment() == 1){
            $server = 'https://medicloud.sg/app/gcal/google_calendar_sync/'.$google_link_code;
        } elseif(StringHelper::Deployment() == 2) {
            $server = 'http://ec2-52-221-188-147.ap-southeast-1.compute.amazonaws.com/app/gcal/google_calendar_sync/'.$google_link_code;
        }else{
            $server = 'http://medicloud.dev/app/gcal/google_calendar_sync/'.$google_link_code;
        }

		$emailDdata['emailName']= 'Calendar Authorization';
		$emailDdata['link']= $server;
		$emailDdata['emailPage']= 'email-templates.calender-oauth';
        $emailDdata['emailTo']= $_POST['gmail'];
    	$emailDdata['emailSubject'] = 'Calendar Authorization';
        EmailHelper::sendEmail($emailDdata);

        $doctor = new Doctor();
	    $doctor->updateDoctor(array('doctorid'=>$doctorid, 'gmail'=>$gmail, 'token'=>NULL, 'google_link' => $authUrl, 'google_link_code' => $google_link_code));

	}

	public function getClientToken()
	{	
		//dd($_GET['code']);
		define('SCOPES', implode(' ', array(
		  Google_Service_Calendar::CALENDAR,'https://www.googleapis.com/auth/userinfo.email')
		));

		$google_client = $this->getconfig();
		$google_client->setScopes(SCOPES);
		$google_client->authenticate($_GET['code']);
	 	$token = $google_client->getAccessToken();
		$tmp = json_decode($token);



		$ticket = $google_client->verifyIdToken($tmp->id_token);
	  if ($ticket) {
	    $data = $ticket->getAttributes();
	    $gmail = $data['payload']['email']; // user ID



	    $doctor = new Doctor();
	    $doctor->updateDoctorByGmail(array('gmail'=>$gmail, 'token'=>$token));

	    return View::make('email-templates.calender-oauth-back');
  		
	  }



		

	}
// nhr ud 2016-1-28
	public function insertEvent($bookArray,$findDoctorDetails)
	{	
		$google_client = $this->getconfig();
		try {
			$token = $findDoctorDetails->token;
			$google_client->setAccessToken($token);
	        if ($google_client->isAccessTokenExpired()) {
				    $google_client->refreshToken($google_client->getRefreshToken());
				    $token = $google_client->getAccessToken();
				  }
			$google_client->setAccessToken($token);
			
		} catch (Exception $e) {
			 
		}


		$calendarService = new Google_Service_Calendar($google_client);
    	$calendarList = $calendarService->calendarList;

    	$date = date('Y-m-d',$bookArray['bookdate']);
    	$stime = date('H:i:s',$bookArray['starttime']);
    	$etime = date('H:i:s',$bookArray['endtime']);
    	$stime = $date.'T'.$stime;
    	$etime = $date.'T'.$etime;
    	$remarks=$bookArray['remarks'];
    	$patient=$bookArray['patient'];   
    	$procedurename=new ClinicProcedures();
    	$dsc=$procedurename->ClinicProcedureByID($bookArray['procedureid']);
    	$proname=$dsc->Name;
    	$calenderdescription=$remarks.' - '.$proname;
		$event = new Google_Service_Calendar_Event(array(
		    'summary' => $patient.' - '.'New Mednefits Appointment '.'( '.$proname.' )',
		    'location' => 'Mednefits',
		    'description' =>$calenderdescription,
		    'colorId' => '10',
		    'start' => array(
		    'dateTime' => $stime,
		    'timeZone' => 'Asia/Singapore',
		    ),
		    'end' => array(
		    'dateTime' => $etime,
		     'timeZone' => 'Asia/Singapore',
		    )
		));
		
		$calendarId = 'primary';

	    try {
	      $createdEvent = $calendarService->events->insert($calendarId,$event);
	      return $createdEvent->id;
	    } catch (Exception $e) {
	      // var_dump($e->getMessage());
	    }
	    // echo 'Event Successfully Added with ID: '.$createdEvent->getId();
		}

 public function revokeToken()
 {	
 	$input = input::all();
 	$doctorid = $input['doctorid'];
 	
 	$doctor = new Doctor();
	$data = $doctor->FindDoctor($doctorid);
 	$google_client = $this->getconfig();

	try {
		
	    $dbtoken=$data->token;
	       
		$access_token = json_decode($dbtoken)->access_token;

	 	$status = $google_client->revokeToken($access_token);
	 	$rmevents = new ExtraEvents();
    $rmevents->removeEvent($doctorid);   
	} catch (Exception $e) {}
	//  	if ($status) {
	 		$doctor->updateDoctor(array('doctorid'=>$doctorid, 'token'=>NULL, 'gmail'=>NULL, 'google_link_code' => NULL, 'google_link' => NULL));
	 	// 	return 1;
	 	// }else {
	 	// 	return 0;
	 	// }
		

 }


public function loadTokendGmail()
{
	$input = input::all();
 	$doctorid = $input['doctorid'];
 	
 	$doctor = new Doctor();
	$data = $doctor->FindDoctor($doctorid);

	return json_encode($data);
}


public function getdoctorcalender_data($cur_date,$doctorid)
    {   
        $google_client = $this->getconfig();
        

        $dbtoken = new Doctor();
        $Find = $dbtoken->FindDoctor($doctorid);

       $dbtoken=$Find->token;
       
      // dd($dbtoken);
       // dd($dbtoken);
        try {
	
        $google_client->setAccessToken($dbtoken);

        $calendarService = new Google_Service_Calendar($google_client);
        		
        		$sdate = date('c', strtotime($cur_date));
				$date = strtotime($sdate);
				$date = strtotime("+7 day", $date);
				$max  = date('c', $date);

                $calendarId = 'primary';
                $optParams = array(
	               // 'maxResults' => 100,	
	               'singleEvents' => TRUE,
	               'orderBy' => 'startTime',
	               'timeMin' => $sdate,
	               'timeMax' => $max,
	               // 'timeZone' => 'Asia/Singapore',
	               
                  );
                  $results = $calendarService->events->listEvents($calendarId, $optParams);
                  
                  return $results ;
			} catch (Exception $e) {
				
			}

               
                   
   }

   public function getGoogleCodeLink($code)
   {
   		$doctor = new Doctor();
   		$data = [];
   		$result = $doctor->getGoogleCodeLink($code);
   		if($result) {
	   		$data['link'] = $result->google_link;
	   		return View::make('email-templates.calendar-authorize', $data);
   		} else {
   			return "Need google token";
   		}   
   	}


   public function checkuniqueGmail()
   {	
   		$gmail = $_POST['gmail'];
   		$doctor = new Doctor();
        $gmail=$doctor->findUniqueGmail($gmail);
        
        if ($gmail==NULL) {
        	return 1;
        }else{
        	return 0;
        }
        
   }

	public function removeEvent($doctorid,$eventID)
	{
		$google_client = $this->getconfig();
		$data=new Doctor();
	  	$find=$data->FindDoctor($doctorid);
	    $dbtoken=$find->token;
	    $google_client->setAccessToken($dbtoken);
		$calendarService = new Google_Service_Calendar($google_client);

		$calendarService->events->delete('primary',$eventID);
	      
	}
	
	
}
