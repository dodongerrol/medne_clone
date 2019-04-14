<?php

class staffController extends BaseController {

	public function ajaxGetDoctorSettingTab(){

		return View::make('settings.staff.staff-tab-panel');

	}

	public function ajaxGetStaffSettingTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){ 
				$input = Input::all();
	            $data['staff'] = Settings_Library::getStaffDetails($input['staff_id']);

				return View::make('settings.staff.staff-member-details', $data);

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}

// ````````````````````````````````````````````````````````````````````````````````````````

	public function ajaxGetStaffDetailsTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){ 
            	$input = Input::all();
            	$data['doctorDetails'] = Doctor_Library::FindDoctor($input['doctor_id']);
                // dd($data);
				return View::make('settings.staff.staff-details',$data);   

		 }else{
                return Redirect::to('provider-portal-login');
            }
		

	}

// ```````````````````````````````````````````````````````````````````````````````````````````````````````

	public function ajaxGetStaffServicesTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$input = Input::all();

		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){ 
               
		$data['services'] = Clinic_Library::FindClinicProcedures($getSessionData->Ref_ID);
		$data2['doctor_services'] = Doctor_Library::FindDoctorProcedures($getSessionData->Ref_ID,$input['id']);

		return View::make('settings.staff.staff-service',$data,$data2);

		 }else{
                return Redirect::to('provider-portal-login');
            }

		

	}

	public function ajaxGetStaffWorkingHoursTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){ 
            	$input = Input::all();
            	Settings_Library::addDoctorManageTimeSlots();
            	$data['findDoctorTimes'] = General_Library::FindAllClinicTimesNew(2,$input['doctor_id'],strtotime(date('d-m-Y')));
				return View::make('settings.staff.staff-working-hours',$data);

		}else{
                return Redirect::to('provider-portal-login');
            }

	}

	public function ajaxGetStaffBreaksTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $input = Input::all();
                Settings_Library::addDoctorManageTimeSlots();
            	$data['doctorBreaks'] = Settings_Library::getdoctorBreaks();
                $data['findDoctorTimes'] = General_Library::FindAllClinicTimesNew(2,$input['doctorid'],strtotime(date('d-m-Y')));
                // dd($data['findDoctorTimes']);
				return View::make('settings.staff.staff-breaks',$data);
		}else{
                return Redirect::to('provider-portal-login');
            }

	}

	public function ajaxGetStaffTimeOffTab(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$input = Input::all();

		$getSessionData = StringHelper::getMainSession(3);
            
        if($getSessionData != FALSE){
               
			$data['Holiday'] = General_Library::FindExistingClinicHolidays(2,$input['doctor_id']);

			return View::make('settings.staff.staff-time-off',$data);

		 }else{

           	return Redirect::to('provider-portal-login');
        }

	}


	public function addStaff()
	{
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){ 
			Settings_Library::addStaff($getSessionData->Ref_ID);
            $data['doctors'] = Clinic_Library::FindAllClinicDoctors($getSessionData->Ref_ID);
            $data['staff'] = Settings_Library::getClinicStaff($getSessionData->Ref_ID);
// dd($data['staff']);
				return View::make('settings.staff.staff-main',$data);   

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}

// ````````````````````````````````````````````````````````````````````````````````````````````````````````````
	public function addDoctor()
	{
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){ 
			Settings_Library::addDoctor($getSessionData->Ref_ID);
            $data['doctors'] = Clinic_Library::FindAllClinicDoctors($getSessionData->Ref_ID);
            $data['staff'] = Settings_Library::getClinicStaff($getSessionData->Ref_ID);
// dd($data['staff']);
				return View::make('settings.staff.staff-main',$data);   

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}
// ``````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````

	public function updateDoctor()
	{
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){

            $input = Input::all();

            if($input['status'] == 'new-pin'){

                $doctorDetails = Doctor_Library::FindDoctor($input['doctor_id']);
                // dd($doctorDetails);

                if ($doctorDetails ->pin == $input['old_pin']){

                    Settings_Library::updateDoctor();
                    return 1;
                }
                else {
                    return 0;
                }
            }
            else {
                
                Settings_Library::updateDoctor();
            }

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}

// ````````````````````````````````````````````````````````````````````````````````````````````````


	public function updateStaff()
	{
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){

             $input = Input::all();

            if($input['status'] == 'new-pin'){

                $staff = Settings_Library::getStaffDetails($input['staff_id']);
                // dd($staff[0] ->pin_no);

                if ($staff[0] ->pin_no == $input['old_pin']){

                    Settings_Library::updateStaff();
                    $data['staff'] = Settings_Library::getStaffDetails($input['staff_id']);
                    return View::make('settings.staff.staff-member-details', $data);
                }
                else {
                    return 0;
                }
            }
            else {

                Settings_Library::updateStaff();
                $data['staff'] = Settings_Library::getStaffDetails($input['staff_id']);
                return View::make('settings.staff.staff-member-details', $data);
            }

		 }else{
                return Redirect::to('provider-portal-login');
            }
	}


// ```````````````````````````````````````````````````````````````````````````````````````

	public function UpdateDoctorService(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $doctorService = Settings_Library::UpdateDoctorService($getSessionData);
                return $doctorService;
            }else{
                return 0;
            }
	}

	public function UpdateDoctorAllService(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $AllService = Settings_Library::UpdateDoctorAllService($getSessionData);
                return $AllService;
            }else{
                return 0;
            }
	}



	public function UpdateWorkingHours(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                Settings_Library::UpdateWorkingHours($getSessionData->Ref_ID);
            }else{
                return 0;
            }
	}


	public function UpdateWorkingHoursStatus(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                return Settings_Library::UpdateWorkingHoursStatus();
            }else{
                return 0;
            }
	}


	public function addBreak(){

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $data['server'] = $protocol.$hostName;
            $data['date'] = new DateTime();
            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                Settings_Library::addBreak();
                $data['doctorBreaks'] = Settings_Library::getdoctorBreaks();
                $input = Input::all();
                $data['findDoctorTimes'] = General_Library::FindAllClinicTimesNew(2,$input['doctorid'],strtotime(date('d-m-Y')));

				return View::make('settings.staff.staff-breaks',$data);
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	public function updateBreak(){

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $data['server'] = $protocol.$hostName;
            $data['date'] = new DateTime();

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                Settings_Library::updateBreak();
                $data['doctorBreaks'] = Settings_Library::getdoctorBreaks();
                $input = Input::all();
                $data['findDoctorTimes'] = General_Library::FindAllClinicTimesNew(2,$input['doctorid'],strtotime(date('d-m-Y')));

				return View::make('settings.staff.staff-breaks',$data);
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	public function removeBreak(){

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $data['server'] = $protocol.$hostName;
            $data['date'] = new DateTime();
            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                Settings_Library::removeBreak();
                $data['doctorBreaks'] = Settings_Library::getdoctorBreaks();
                $input = Input::all();
                $data['findDoctorTimes'] = General_Library::FindAllClinicTimesNew(2,$input['doctorid'],strtotime(date('d-m-Y')));
                
				return View::make('settings.staff.staff-breaks',$data);
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	/* --- Used to Add Doctor Time-Off --- */

	public function AddDoctorTimeOff(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $addTimeOff = Settings_Library::AddDoctorTimeOff($getSessionData->Ref_ID);
                return $addTimeOff;
            }else{
                return Redirect::to('provider-portal-login');
            }
	}

	public function GetDoctorTimeOff(){

		$HolidayDetails = Settings_Library::GetDoctorTimeOff();
		return json_encode($HolidayDetails);
	}

	public function UpdateDoctorTimeOff(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $updateTimeOff = Settings_Library::UpdateDoctorTimeOff($getSessionData);
                return $updateTimeOff;
            }else{
                return 0;
            }
	}

	public function DeleteDoctorTimeOff(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $DeleteTimeOff = Settings_Library::DeleteDoctorTimeOff($getSessionData);
                return $DeleteTimeOff;
            }else{
                return 0;
            }
	}

	public function UpdateDetailToggal(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $DeleteTimeOff = Settings_Library::UpdateDetailToggal($getSessionData);
                return $DeleteTimeOff;
            }else{
                return 0;
            }
	}

	public function UpdateStaffToggal(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $DeleteTimeOff = Settings_Library::UpdateStaffToggal($getSessionData);
                return $DeleteTimeOff;
            }else{
                return 0;
            }
	}


	public function DeleteDoctorDetail(){

		$getSessionData = StringHelper::getMainSession(3);
            
        if($getSessionData != FALSE){

        	$deleteDoctor = Settings_Library::DeleteDoctorDetail();
            return $deleteDoctor;
        	
		 }else{
                return Redirect::to('provider-portal-login');
            }
	}


	public function DeleteStaffDetail(){

		$getSessionData = StringHelper::getMainSession(3);
            
        if($getSessionData != FALSE){

        	Settings_Library::DeleteStaffDetail();
        	
		 }else{
                return Redirect::to('provider-portal-login');
            }
	}


}

