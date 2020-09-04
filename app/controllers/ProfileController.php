<?php

class ProfileController extends \BaseController {

	public function ajaxGetClinicDetailPanel(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

            	$findClinicDetails = Clinic_Library::FindClinicDetails($getSessionData->Ref_ID);
                

	            $clinicArray = Array_Helper::GetClinicDetailArray($getSessionData,$findClinicDetails);
                /*
                    Added By Stephen
                    Description: Image src must be localy located if currently in DEVELOPMENT Stage
                */
                if (StringHelper::Deployment() == 2 || empty($clinicArray['image'])) {
                    $clinicArray['image'] = URL::asset('assets/images/img-portfolio-place.png');
                }

                $data['clinicdetails'] = $clinicArray;

	            $clinic_type = new CalendarController();
		        $clinic_type = $clinic_type->getClinicTypesAdmin($getSessionData->Ref_ID);
		        $clinic_type = json_decode($clinic_type);
                $data['clinic_type'] = $clinic_type;

				return View::make('settings.profile.clinic-deatail',$data);

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}


	public function ajaxGetBusinessHoursPanel(){
        $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
		      return View::make('settings.profile.clinic-hours-main');
            }else{
                return Redirect::to('provider-portal-login');
            }
	}


    public function ajaxGetclinicPasswordPanel(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
        $getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                $findClinicDetails = Clinic_Library::FindClinicDetails($getSessionData->Ref_ID);

                $clinicArray = Array_Helper::GetClinicDetailArray($getSessionData,$findClinicDetails);
                $data['clinicdetails'] = $clinicArray;

                return View::make('settings.profile.clinic-password',$data);

         }else{
                return Redirect::to('provider-portal-login');
            }
    }

    public function ajaxGetPaymentDetails(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
        $data['invoice_required'] = FALSE;
        $getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                $findClinicDetails = Clinic_Library::FindClinicDetails($getSessionData->Ref_ID);

                $clinicArray = Array_Helper::GetClinicDetailArray($getSessionData,$findClinicDetails);
                $data['clinicdetails'] = $clinicArray;

                return View::make('settings.profile.clinic-payment-details',$data);

         }else{
                return Redirect::to('provider-portal-login');
            }
    }


	public function ajaxGetWebsitePanel(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

	            $data['clinicid'] = $getSessionData->Ref_ID;

				return View::make('settings.profile.website',$data);

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}


	public function ajaxGetSocialPlugPanel(){

		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

	            // $data['staff'] = Settings_Library::getStaffDetails($input['staff_id']);

				return View::make('settings.profile.social-pluging');

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}


	public function UpdateClinicDetails(){

            $getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                $clinicDetailsUpdated = Settings_Library::UpdateClinicProfileDetails($getSessionData);

                return $clinicDetailsUpdated;
            }else{
                return 0;
            }
    }


    public function ajaxGetClinicHoursTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

            	Calendar_Library::addClinicManageTimeSlots($getSessionData->Ref_ID);
        		$data['ClinicTimes'] = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y')));

				return View::make('settings.profile.clinic-business-hours',$data);
		}else{
                return Redirect::to('provider-portal-login');
            }

	}


	public function ajaxGetClinicBreaksTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){
                Calendar_Library::addClinicManageTimeSlots($getSessionData->Ref_ID);
            	$data['clinicBreaks'] = Settings_Library::getClinicBreaks($getSessionData->Ref_ID);
                $data['ClinicTimes'] = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y')));

				return View::make('settings.profile.clinic-breaks',$data);
		}else{
                return Redirect::to('provider-portal-login');
            }

	}

	public function ajaxGetClinicTimeOffTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

        if($getSessionData != FALSE){

			$data['Clinic_Holiday'] = General_Library::FindExistingClinicHolidays(3,$getSessionData->Ref_ID);

			return View::make('settings.profile.clinic-time-off',$data);
		}else{

           	return Redirect::to('provider-portal-login');
        }

	}

	public function addClinicBreak(){
            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $data['server'] = $protocol.$hostName;
            $data['date'] = new DateTime();
            $getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                Settings_Library::addClinicBreak($getSessionData->Ref_ID);

                $data['clinicBreaks'] = Settings_Library::getClinicBreaks($getSessionData->Ref_ID);
                $data['ClinicTimes'] = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y')));

				return View::make('settings.profile.clinic-breaks',$data);
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	public function removeClinicBreak(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                Settings_Library::removeBreak();

                $data['clinicBreaks'] = Settings_Library::getClinicBreaks($getSessionData->Ref_ID);
                $data['ClinicTimes'] = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y')));

				return View::make('settings.profile.clinic-breaks',$data);
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	public function updateClinicBreak(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                Settings_Library::updateBreak();

                $data['clinicBreaks'] = Settings_Library::getClinicBreaks($getSessionData->Ref_ID);
                $data['ClinicTimes'] = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y')));

				return View::make('settings.profile.clinic-breaks',$data);
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	/* ---------- Used to Add Clinic Time-Off ---------- */

	public function AddClinicTimeOff(){

            $getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){
                $addTimeOff = Settings_Library::AddClinicTimeOff($getSessionData->Ref_ID);
                return $addTimeOff;
            }else{
                return 0;
            }
	}

    public function UpdateClinicPassword(){

            $getSessionData = StringHelper::getMainSession(3);

            if($getSessionData != FALSE){

                $input = Input::all();

                $user = new User();
                $findUser = $user->UserProfileByRef($getSessionData->Ref_ID);
                // dd($findUser);

                $old_pass = StringHelper::encode($input['old_pass']);

                if ($findUser->Password == $old_pass ) {

                    $updatePass = Settings_Library::updatePassword($findUser->UserID);

                    return 1;

                } else {

                    return 0;
                }

            }else{

                return Redirect::to('provider-portal-login');
            }
    }

}
