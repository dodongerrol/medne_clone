<?php

class serviceController extends BaseController {

	

/////////////////////////load ajax pages///////////////////////////
	public function ajaxGetEditPage()
	{
		$hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
				$input = Input::all();

				$data['services'] = Clinic_Library::GetClinicProcedure($input['id']);
				$data['doctors'] = Clinic_Library::FindAllClinicDoctors($getSessionData->Ref_ID);
				$data['service_doctors'] = Calendar_Library::FindDoctorProcedures($input['id'],$getSessionData->Ref_ID);

				// dd($data['service_doctors']);

				return View::make('settings.services.form',$data);
			}else{

                return Redirect::to('provider-portal-login');
            }
	}

	// update service position
	public function saveServicePosition( ) 
	{
		$clinicprocedure = new ClinicProcedures();
		return $clinicprocedure->rearrangeServicePosition($_POST['mydata']);
	}
	
	public function saveServices()
	{	
		$hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
				$getSessionData = StringHelper::getMainSession(3);
				$services = Settings_Library::saveServices($getSessionData->Ref_ID);
				$input = Input::all();
				$id = $input['id'];
				if ($id=="null") { /// new insert

					Calendar_Library::addDoctorService($getSessionData->Ref_ID,$services);
				}

				$data['services'] = Clinic_Library::FindClinicProcedures($getSessionData->Ref_ID);
				return View::make('settings.services.list',$data);
			}else{

                return Redirect::to('provider-portal-login');
            }


	}

	public function deleteServices()
	{	

		$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
				$getSessionData = StringHelper::getMainSession(3);
				return Settings_Library::deleteServices($getSessionData->Ref_ID);
			}else{

                return Redirect::to('provider-portal-login');
            }


	}

	public function UpdateDoctorAllService(){

            $getSessionData = StringHelper::getMainSession(3);
            
            if($getSessionData != FALSE){
                $AllService = Settings_Library::UpdateServiceAlldoctors($getSessionData);
                return $AllService;
            }else{
                return 0;
            }
	}





}
