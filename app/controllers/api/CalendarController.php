<?php
use Illuminate\Support\Facades\Input;
class CalendarController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	public function rescheduleAppointmentCheck( )
	{
		$input = Input::all();
		return \calendarLibrary::rescheduleAppointmentCheck($input);
	}

	public function rescheduleAppointment( )
	{
		$input = Input::all();
		$res = calendarLibrary::rescheduleAppointment($input);
		return $res;
	}

	public function checkBookDateResource( )
	{
		$input = Input::all();
		return \calendarLibrary::checkBookDateResource($input);
	}

	public function getGroupResource( )
	{
		$input = Input::all();
		return \calendarLibrary::getGroupResource($input);
	}

	public function getGroupEvents( )
	{	
		$input = Input::all();
		return \calendarLibrary::getGroupEvents($input);
	}

	public function getClinicDoctors($clinicID)
	{
		$doctors = new stdClass();
		$doctoravailability = new DoctorAvailability();
        return $doctoravailability->FindAllClinicDoctors($clinicID);
		return json_encode($doctors);
	}

	public function loadDoctorProcedures($clinicID,$doctorID)
	{
		$procedures = new stdClass();
		$doctorprocedures = new Doctor_Library();
        $procedures = $doctorprocedures->FindDoctorProcedures($clinicID,$doctorID);
		return json_encode($procedures);
	}
	public function getdoctorListWithData()
	{
		$getSessionData = StringHelper::getMainSession(3);
		$doctorList = Calendar_Library::getDoctorListWithAppointMents($getSessionData->Ref_ID);
		return $doctorList;
	}

	public function getDoctorsSchedEvents( )
	{
		// $getSessionData = StringHelper::getMainSession(3);
		$events = Calendar_Library::doctorsScheduleByClinic(4332);
		return $events;
	}

	public function getEvents()
	{
		// return Input::all();
		$input = Input::all();
		$getSessionData = StringHelper::getMainSession(3);
		// if($input['day_view_status'] == "true") {
			// $events = Calendar_Library::getDoctorsSchedEvents($getSessionData->Ref_ID);
		// } else {
			$events = Calendar_Library::getEvents($getSessionData->Ref_ID);
		// }

		return $events;

	}


	public function getGoogleEvents()
	{
		$getSessionData = StringHelper::getMainSession(3);
		$events = Calendar_Library::getGoogleEvents($getSessionData);

		return $events;

	}

	// --------------- set doctor procedure in appiontment popup --------

	public function getDoctorProcedure()
	{
		$allInputs = Input::all();
		$data = Calendar_Library::getDoctorProcedure();

		if( isset( $allInputs['corporate'] ) ){
			$corporate = $allInputs['corporate'];
		}

		if ($data) {

			if( $corporate == false || $corporate == 'false' ){
				$select = '<li style="padding: 5px 15px 5px 15px; color: #555555;">
						<span class="service" id="0">Slot Blocker</span>
						<span class="pull-right">Custom</span>
					  </li>
					  <li id="reserve" style="padding: 5px 15px 5px 15px; color: #555555;" >
						<span>Apppointment wihout SMS Notification</span>
						<span class="pull-right">Custom</span>
					</li>
		        	  <li class="divider"></li>';
			}else{
				$select = '';
			}
           

           foreach ($data as $key => $value) {
	        	$select.=   '<li style="padding: 5px 15px 5px 15px; color: #555555;">
								<span class="service" id="'.$value->ProcedureID.'">'.$value->Name.'</span>
								<span class="pull-right">'.$value->Duration.' '.$value->Duration_Format.'</span>
							</li>';
		    }

       } else {

       		if( $corporate == false || $corporate == 'false' ){
				$select = '<li style="padding: 5px 15px 5px 15px; color: #555555;">
						<span class="service" id="0">Slot Blocker</span>
						<span class="pull-right">Custom</span>
					   </li>
					   <li id="reserve" style="padding: 5px 15px 5px 15px; color: #555555;" >
							<span>Reserve Booking</span>
							<span class="pull-right">Custom</span>
						</li>
						<li class="divider"></li>
						<li id="null" style="padding: 5px 15px 5px 15px; color: #555555;" >
								<span>No Services for this Doctor</span>
							</li>';
			}else{
				$select = '<li id="null" style="padding: 5px 15px 5px 15px; color: #555555;" >
								<span>No Services for this Doctor</span>
							</li>';
			}

			
		}

        return $select;

	}



	public function getProcedureDetails()
	{
		$allInputs = Input::all();
		$procedure = General_Library::FindClinicProcedure($allInputs['procedureID']);

		return json_encode($procedure);
	}

// ----------------------------------------------

	public function getAllUsers ()
	{
		$users = new stdClass();
		// dd ($users);

		$users = Calendar_Library::getAllUsers();

		return json_encode($users);

	}

// ----------------------------------------------


	public function saveAppointment()
	{
		 $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Calendar_Library::saveAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
	}

	public function updateAppointment()
	{
		 $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $booking = Calendar_Library::updateAppointment($getSessionData);
                return $booking;
            }else{
                return 0;
            }
	}

	public function saveBlocker($value='')
	{
		$getSessionData = StringHelper::getMainSession(3);
		if($getSessionData != FALSE){
			$blocker = Calendar_Library::saveBlocker($getSessionData->Ref_ID);
		return $blocker;
            }else{
                return 0;
            }
	}



	public function blockUnavailable()
	{
		$getSessionData = StringHelper::getMainSession(3);
		$block = Calendar_Library::blockUnavailable($getSessionData->Ref_ID);
		return $block;
	}



	public function getAppointmentDetails()
	{
		$appointment = Calendar_Library::getAppointmentDetails();
		return json_encode($appointment);
	}

	public function getExtraEventDetails()
	{
		$extraEvent = Calendar_Library::getExtraEventDetails();
		return json_encode($extraEvent);
	}

	public function deleteBlockerDetails()
	{
		$removeEvent = Calendar_Library::deleteBlockerDetails();
		// return $removeEvent;
	}

	public function deleteAppointmentDetails(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Calendar_Library::deleteAppointmentDetails($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
    }

    public function concludedAppointment(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Calendar_Library::concludedAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
    }

    public function concludedAppointmentFromTransaction(){
      $getSessionData = StringHelper::getMainSession(3);
      if($getSessionData != FALSE){
          $openingTimes = Calendar_Library::concludedAppointmentFromTransaction($getSessionData);
          return $openingTimes;
      }else{
          return 0;
      }
    }

    public function concludedAppointmentFromClaimTransaction($data, $appointment_id){
      $getSessionData = StringHelper::getMainSession(3);
      if($getSessionData != FALSE){
          $openingTimes = Calendar_Library::concludedAppointmentFromClaimTransaction($getSessionData, $data, $appointment_id);
          return $openingTimes;
      }else{
          return 0;
      }
    }

    public function NoShowAppointment(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Calendar_Library::NoShowAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
    }


    public function updateOnDrag()
    {
    	$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $res = Calendar_Library::updateOnDrag($getSessionData);
                return $res;
            }else{
                return 0;
            }

    }

    public function updateOnBlockerDrag()
    {
    	$getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $res = Calendar_Library::updateOnBlockerDrag($getSessionData);
                return $res;
            }else{
                return 0;
            }

    }


    public function getClinicTypes()
	{
		$clinic_type = new stdClass();
		$findClinicTypes = new Admin_Clinic_Type();
        $clinic_type = $findClinicTypes->GetAllClinicTypes();

		return json_encode($clinic_type);
	}

	public function getClinicTypesAdmin()
	{
		$clinic_type = new stdClass();
		$findClinicTypes = new Admin_Clinic_Type();
        $clinic_type = $findClinicTypes->getClinicTypesFromWeb();

		return json_encode($clinic_type);
	}

	public function getWebClinicTypes()
	{
		$clinic_type = new stdClass();
		$findClinicTypes = new Admin_Clinic_Type();
        $clinic_type = $findClinicTypes->getClinicTypesFromWeb();

		return json_encode($clinic_type);
	}


	public function getClinicDetails ()
	{
		$getSessionData = StringHelper::getMainSession(3);
    // return var_dump($getSessionData);
            if($getSessionData != FALSE){

                $clinic = Calendar_Library::getClinicDetails($getSessionData->Ref_ID);
				// dd ($clinic);
				// return $clinic;
				return json_encode($clinic);

            }else{
                return 0;
            }

	}


	public function validatePin()
	{
		$getSessionData = StringHelper::getMainSession(3);

	            if($getSessionData != FALSE){

	                $clinic = Calendar_Library::validatePin($getSessionData->Ref_ID);
					// dd ($clinic);

					return $clinic;

	            }else{
	                return 0;
	            }
	}


	public function getClinicPinStatus()
	{
		$getSessionData = StringHelper::getMainSession(3);

	            if($getSessionData != FALSE){

	                $clinic = Calendar_Library::getClinicPinStatus($getSessionData->Ref_ID);

					return $clinic;

	            }else{
	                return 0;
	            }
	}


	public function loadAppointmentCount()
	{
		$getSessionData = StringHelper::getMainSession(3);

		if($getSessionData) {
			$ua = new UserAppoinment();
			$ua_count = $ua->getClinicAppointments($getSessionData->Ref_ID);

			return count($ua_count);
		} else {
			return 0;
		}
	}

	public function getExistingAppointments($id)
	{
		$dates = [];
		$monday = strtotime('last Monday');
		$sunday = strtotime('next Monday');
		// return $sunday;
		// $date_start = new \DateTime($monday);
		// $date_end = new \DateTime($sunday);
		// $interval = new DateInterval('P1D');
		// $daterange = new DatePeriod($date_start, $interval ,$date_end);

		$appointment = new UserAppoinment();

		// foreach($daterange as $key => $date){
		// 	$dates[] = $appointment->getExistingAppointments($id, strtotime($date->format('Y-m-d');
		// }

		return $appointment->getExistingAppointments($id, $monday, $sunday);
	}

} //end of class
